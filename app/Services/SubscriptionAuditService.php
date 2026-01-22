<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionAuditLog;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class SubscriptionAuditService
{
    /**
     * Log a subscription event
     */
    public function log(
        Subscription $subscription,
        string $event,
        ?Plan $previousPlan = null,
        ?Plan $newPlan = null,
        ?string $previousStatus = null,
        ?string $newStatus = null,
        ?float $amount = null,
        ?float $proratedAmount = null,
        ?float $creditAmount = null,
        ?string $description = null,
        array $metadata = []
    ): SubscriptionAuditLog {
        $user = Auth::user();
        $request = request();

        return SubscriptionAuditLog::create([
            'subscription_id' => $subscription->id,
            'tenant_id' => $subscription->tenant_id,
            'user_id' => $user?->id,
            'event' => $event,
            'previous_plan_id' => $previousPlan?->id,
            'new_plan_id' => $newPlan?->id,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'amount' => $amount,
            'prorated_amount' => $proratedAmount,
            'credit_amount' => $creditAmount,
            'description' => $description ?? $this->generateDescription($event, $previousPlan, $newPlan),
            'metadata' => $metadata,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
        ]);
    }

    /**
     * Log subscription creation
     */
    public function logCreated(Subscription $subscription): SubscriptionAuditLog
    {
        $description = "Subscrição criada com o plano {$subscription->plan->name}";
        if ($subscription->onTrial()) {
            $description .= " (período de teste de {$subscription->daysRemainingInTrial()} dias)";
        }

        return $this->log(
            $subscription,
            SubscriptionAuditLog::EVENT_CREATED,
            null,
            $subscription->plan,
            null,
            $subscription->status,
            $subscription->plan->price,
            null,
            null,
            $description,
            [
                'trial_days' => $subscription->plan->trial_days,
                'billing_period' => $subscription->plan->billing_period,
            ]
        );
    }

    /**
     * Log subscription upgrade
     */
    public function logUpgraded(
        Subscription $subscription,
        Plan $previousPlan,
        float $proratedAmount
    ): SubscriptionAuditLog {
        $description = "Upgrade de {$previousPlan->name} para {$subscription->plan->name}";

        return $this->log(
            $subscription,
            SubscriptionAuditLog::EVENT_UPGRADED,
            $previousPlan,
            $subscription->plan,
            null,
            $subscription->status,
            $subscription->plan->price,
            $proratedAmount,
            null,
            $description,
            [
                'days_remaining' => $subscription->daysRemainingInPeriod(),
                'immediate' => true,
            ]
        );
    }

    /**
     * Log subscription downgrade scheduled
     */
    public function logDowngradeScheduled(
        Subscription $subscription,
        Plan $currentPlan,
        Plan $newPlan
    ): SubscriptionAuditLog {
        $description = "Downgrade agendado de {$currentPlan->name} para {$newPlan->name} para {$subscription->plan_change_scheduled_for->format('d/m/Y')}";

        return $this->log(
            $subscription,
            SubscriptionAuditLog::EVENT_DOWNGRADE_SCHEDULED,
            $currentPlan,
            $newPlan,
            $subscription->status,
            $subscription->status,
            null,
            null,
            null,
            $description,
            [
                'scheduled_for' => $subscription->plan_change_scheduled_for->toDateTimeString(),
                'days_until_change' => now()->diffInDays($subscription->plan_change_scheduled_for),
            ]
        );
    }

    /**
     * Log downgrade executed
     */
    public function logDowngraded(
        Subscription $subscription,
        Plan $previousPlan
    ): SubscriptionAuditLog {
        $description = "Downgrade de {$previousPlan->name} para {$subscription->plan->name} executado";

        return $this->log(
            $subscription,
            SubscriptionAuditLog::EVENT_DOWNGRADED,
            $previousPlan,
            $subscription->plan,
            null,
            $subscription->status,
            $subscription->plan->price,
            null,
            null,
            $description
        );
    }

    /**
     * Log downgrade canceled
     */
    public function logDowngradeCanceled(
        Subscription $subscription,
        Plan $scheduledPlan
    ): SubscriptionAuditLog {
        $description = "Downgrade agendado para {$scheduledPlan->name} foi cancelado";

        return $this->log(
            $subscription,
            SubscriptionAuditLog::EVENT_DOWNGRADE_CANCELED,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $description
        );
    }

    /**
     * Log subscription canceled
     */
    public function logCanceled(
        Subscription $subscription,
        bool $immediately = false,
        ?float $creditAmount = null
    ): SubscriptionAuditLog {
        if ($immediately) {
            $description = "Subscrição cancelada imediatamente";
            $event = SubscriptionAuditLog::EVENT_CANCELED_IMMEDIATELY;
        } else {
            $description = "Subscrição cancelada (válida até {$subscription->ends_at->format('d/m/Y')})";
            $event = SubscriptionAuditLog::EVENT_CANCELED;
        }

        return $this->log(
            $subscription,
            $event,
            null,
            null,
            null,
            $subscription->status,
            null,
            null,
            $creditAmount,
            $description,
            [
                'canceled_at' => $subscription->canceled_at->toDateTimeString(),
                'ends_at' => $subscription->ends_at?->toDateTimeString(),
                'immediately' => $immediately,
                'days_remaining' => $immediately ? 0 : $subscription->daysRemainingInPeriod(),
            ]
        );
    }

    /**
     * Log subscription resumed
     */
    public function logResumed(Subscription $subscription): SubscriptionAuditLog
    {
        $description = "Subscrição retomada";

        return $this->log(
            $subscription,
            SubscriptionAuditLog::EVENT_RESUMED,
            null,
            null,
            'canceled',
            $subscription->status,
            null,
            null,
            null,
            $description
        );
    }

    /**
     * Log subscription renewed
     */
    public function logRenewed(Subscription $subscription, ?Plan $previousPlan = null): SubscriptionAuditLog
    {
        $description = "Subscrição renovada";
        if ($previousPlan && $previousPlan->id !== $subscription->plan_id) {
            $description .= " com mudança para {$subscription->plan->name}";
        }

        return $this->log(
            $subscription,
            SubscriptionAuditLog::EVENT_RENEWED,
            $previousPlan,
            $subscription->plan,
            null,
            $subscription->status,
            $subscription->plan->price,
            null,
            null,
            $description,
            [
                'new_period_start' => $subscription->current_period_start->toDateTimeString(),
                'new_period_end' => $subscription->current_period_end->toDateTimeString(),
            ]
        );
    }

    /**
     * Log trial started
     */
    public function logTrialStarted(Subscription $subscription): SubscriptionAuditLog
    {
        $description = "Período de teste iniciado ({$subscription->daysRemainingInTrial()} dias)";

        return $this->log(
            $subscription,
            SubscriptionAuditLog::EVENT_TRIAL_STARTED,
            null,
            $subscription->plan,
            null,
            'trialing',
            null,
            null,
            null,
            $description,
            [
                'trial_ends_at' => $subscription->trial_ends_at->toDateTimeString(),
            ]
        );
    }

    /**
     * Log trial converted to active
     */
    public function logTrialConverted(Subscription $subscription): SubscriptionAuditLog
    {
        $description = "Período de teste convertido em subscrição ativa";

        return $this->log(
            $subscription,
            SubscriptionAuditLog::EVENT_TRIAL_CONVERTED,
            null,
            null,
            'trialing',
            'active',
            null,
            null,
            null,
            $description
        );
    }

    /**
     * Log trial expired
     */
    public function logTrialExpired(Subscription $subscription): SubscriptionAuditLog
    {
        $description = "Período de teste expirou";

        return $this->log(
            $subscription,
            SubscriptionAuditLog::EVENT_TRIAL_EXPIRED,
            null,
            null,
            'trialing',
            'expired',
            null,
            null,
            null,
            $description
        );
    }

    /**
     * Log subscription expired
     */
    public function logExpired(Subscription $subscription): SubscriptionAuditLog
    {
        $description = "Subscrição expirou";

        return $this->log(
            $subscription,
            SubscriptionAuditLog::EVENT_EXPIRED,
            null,
            null,
            null,
            'expired',
            null,
            null,
            null,
            $description
        );
    }

    /**
     * Generate a description for an event
     */
    private function generateDescription(string $event, ?Plan $previousPlan, ?Plan $newPlan): string
    {
        return match($event) {
            SubscriptionAuditLog::EVENT_CREATED => 'Subscrição criada',
            SubscriptionAuditLog::EVENT_UPGRADED => "Upgrade de plano",
            SubscriptionAuditLog::EVENT_DOWNGRADED => "Downgrade de plano",
            SubscriptionAuditLog::EVENT_DOWNGRADE_SCHEDULED => "Downgrade agendado",
            SubscriptionAuditLog::EVENT_DOWNGRADE_CANCELED => "Downgrade cancelado",
            SubscriptionAuditLog::EVENT_CANCELED => "Subscrição cancelada",
            SubscriptionAuditLog::EVENT_CANCELED_IMMEDIATELY => "Subscrição cancelada imediatamente",
            SubscriptionAuditLog::EVENT_RESUMED => "Subscrição retomada",
            SubscriptionAuditLog::EVENT_RENEWED => "Subscrição renovada",
            SubscriptionAuditLog::EVENT_TRIAL_STARTED => "Período de teste iniciado",
            SubscriptionAuditLog::EVENT_TRIAL_CONVERTED => "Teste convertido em ativo",
            SubscriptionAuditLog::EVENT_TRIAL_EXPIRED => "Período de teste expirou",
            SubscriptionAuditLog::EVENT_EXPIRED => "Subscrição expirou",
            default => ucfirst(str_replace('_', ' ', $event)),
        };
    }

    /**
     * Get audit logs for a tenant
     */
    public function getTenantAuditLogs(int $tenantId, int $limit = 50)
    {
        return SubscriptionAuditLog::with(['subscription', 'user', 'previousPlan', 'newPlan'])
            ->forTenant($tenantId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit logs for a subscription
     */
    public function getSubscriptionAuditLogs(int $subscriptionId)
    {
        return SubscriptionAuditLog::with(['user', 'previousPlan', 'newPlan'])
            ->forSubscription($subscriptionId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
