<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\TenantCredit;
use App\Models\SubscriptionAuditLog;
use Carbon\Carbon;

class CreditService
{
    /**
     * Add credit to tenant
     */
    public function addCredit(
        Tenant $tenant,
        float $amount,
        string $type,
        string $description,
        ?Subscription $subscription = null,
        ?SubscriptionAuditLog $auditLog = null,
        ?Carbon $expiresAt = null,
        array $metadata = []
    ): TenantCredit {
        $balanceBefore = $tenant->getAvailableCreditsBalance();
        $balanceAfter = $balanceBefore + $amount;

        return TenantCredit::create([
            'tenant_id' => $tenant->id,
            'subscription_id' => $subscription?->id,
            'audit_log_id' => $auditLog?->id,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'type' => $type,
            'status' => TenantCredit::STATUS_PENDING,
            'description' => $description,
            'metadata' => $metadata,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Calculate prorated refund for cancellation
     */
    public function calculateCancellationCredit(Subscription $subscription): float
    {
        // Se já passou o período, não há reembolso
        if (!$subscription->current_period_end || $subscription->current_period_end->isPast()) {
            return 0;
        }

        // Se é plano grátis, não há reembolso
        if ($subscription->plan->isFree()) {
            return 0;
        }

        // Calcular dias restantes no período
        $totalDays = $subscription->current_period_start->diffInDays($subscription->current_period_end);
        $daysRemaining = now()->diffInDays($subscription->current_period_end);

        // Se não há dias restantes, não há reembolso
        if ($daysRemaining <= 0 || $totalDays <= 0) {
            return 0;
        }

        // Calcular valor proporcional
        $dailyRate = $subscription->plan->price / $totalDays;
        $creditAmount = $dailyRate * $daysRemaining;

        return round($creditAmount, 2);
    }

    /**
     * Process cancellation credit
     */
    public function processCancellationCredit(
        Subscription $subscription,
        SubscriptionAuditLog $auditLog,
        bool $immediately = false
    ): ?TenantCredit {
        // Se não é cancelamento imediato, não há crédito
        if (!$immediately) {
            return null;
        }

        $creditAmount = $this->calculateCancellationCredit($subscription);

        // Se não há crédito a processar
        if ($creditAmount <= 0) {
            return null;
        }

        // Criar crédito com validade de 12 meses
        $credit = $this->addCredit(
            $subscription->tenant,
            $creditAmount,
            TenantCredit::TYPE_CANCELLATION_CREDIT,
            "Crédito de cancelamento da subscrição {$subscription->plan->name}",
            $subscription,
            $auditLog,
            now()->addYear(),
            [
                'plan_name' => $subscription->plan->name,
                'plan_price' => $subscription->plan->price,
                'days_remaining' => $subscription->daysRemainingInPeriod(),
                'canceled_at' => now()->toDateTimeString(),
            ]
        );

        return $credit;
    }

    /**
     * Apply credits to a payment
     */
    public function applyCredits(Tenant $tenant, float $amount): float
    {
        $credits = $tenant->credits()
            ->available()
            ->orderBy('expires_at', 'asc') // Usar créditos que expiram primeiro
            ->orderBy('created_at', 'asc')
            ->get();

        $remainingAmount = $amount;

        foreach ($credits as $credit) {
            if ($remainingAmount <= 0) {
                break;
            }

            $creditToUse = min($credit->amount, $remainingAmount);

            // Se usar todo o crédito, marcar como aplicado
            if ($creditToUse >= $credit->amount) {
                $credit->markAsApplied();
            } else {
                // Criar um novo registro de uso parcial
                $this->addCredit(
                    $tenant,
                    -$creditToUse, // Negativo para indicar uso
                    TenantCredit::TYPE_USAGE,
                    "Uso parcial de crédito (ID: {$credit->id})",
                    null,
                    null,
                    null,
                    [
                        'original_credit_id' => $credit->id,
                        'amount_used' => $creditToUse,
                    ]
                );
                $credit->markAsApplied();
            }

            $remainingAmount -= $creditToUse;
        }

        return max(0, $remainingAmount);
    }

    /**
     * Get available credits for tenant
     */
    public function getAvailableCredits(Tenant $tenant)
    {
        return $tenant->credits()
            ->available()
            ->orderBy('expires_at', 'asc')
            ->get();
    }

    /**
     * Get credits summary for tenant
     */
    public function getCreditsSummary(Tenant $tenant): array
    {
        $availableCredits = $this->getAvailableCredits($tenant);
        $totalBalance = $tenant->getAvailableCreditsBalance();

        $expiringSoon = $availableCredits->filter(function ($credit) {
            return $credit->expires_at && $credit->expires_at->lte(now()->addDays(30));
        });

        return [
            'total_balance' => $totalBalance,
            'available_credits_count' => $availableCredits->count(),
            'expiring_soon' => $expiringSoon->sum('amount'),
            'expiring_soon_count' => $expiringSoon->count(),
            'credits' => $availableCredits,
        ];
    }

    /**
     * Expire old credits
     */
    public function expireOldCredits(): int
    {
        $expiredCredits = TenantCredit::expired()->get();

        foreach ($expiredCredits as $credit) {
            $credit->markAsExpired();
        }

        return $expiredCredits->count();
    }

    /**
     * Add promotional credit
     */
    public function addPromotionalCredit(
        Tenant $tenant,
        float $amount,
        string $description,
        ?Carbon $expiresAt = null
    ): TenantCredit {
        return $this->addCredit(
            $tenant,
            $amount,
            TenantCredit::TYPE_PROMOTIONAL,
            $description,
            null,
            null,
            $expiresAt ?? now()->addYear()
        );
    }

    /**
     * Calculate prorated upgrade cost
     */
    public function calculateUpgradeProration(
        Subscription $subscription,
        float $newPlanPrice,
        float $oldPlanPrice
    ): float {
        // Se não há período atual, cobrar preço total
        if (!$subscription->current_period_end) {
            return $newPlanPrice;
        }

        // Calcular dias restantes
        $daysRemaining = now()->diffInDays($subscription->current_period_end);
        $totalDays = $subscription->current_period_start->diffInDays($subscription->current_period_end);

        if ($totalDays <= 0) {
            return $newPlanPrice;
        }

        // Crédito do plano antigo (proporcional aos dias restantes)
        $oldPlanDailyRate = $oldPlanPrice / $totalDays;
        $oldPlanCredit = $oldPlanDailyRate * $daysRemaining;

        // Custo do novo plano (proporcional aos dias restantes)
        $newPlanDailyRate = $newPlanPrice / $totalDays;
        $newPlanCost = $newPlanDailyRate * $daysRemaining;

        // Diferença a pagar
        $prorationAmount = $newPlanCost - $oldPlanCredit;

        return max(0, round($prorationAmount, 2));
    }
}
