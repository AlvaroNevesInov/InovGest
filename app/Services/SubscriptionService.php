<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Models\SubscriptionUsageHistory;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    protected SubscriptionAuditService $auditService;
    protected CreditService $creditService;

    public function __construct(
        SubscriptionAuditService $auditService,
        CreditService $creditService
    ) {
        $this->auditService = $auditService;
        $this->creditService = $creditService;
    }
    /**
     * Create a new subscription for a tenant
     */
    public function createSubscription(Tenant $tenant, Plan $plan): Subscription
    {
        return DB::transaction(function () use ($tenant, $plan) {
            // Calculate trial period
            $trialEndsAt = $plan->trial_days > 0
                ? Carbon::now()->addDays($plan->trial_days)
                : null;

            // Create subscription
            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => $trialEndsAt ? 'trialing' : 'active',
                'trial_ends_at' => $trialEndsAt,
                'current_period_start' => Carbon::now(),
                'current_period_end' => $this->calculatePeriodEnd($plan),
                'next_billing_date' => $trialEndsAt ?? $this->calculatePeriodEnd($plan),
            ]);

            // Initialize usage limits
            $this->initializeUsageLimits($subscription);

            // Log subscription creation
            $this->auditService->logCreated($subscription);

            // Record initial usage snapshot
            SubscriptionUsageHistory::recordSnapshot($subscription);

            return $subscription;
        });
    }

    /**
     * Upgrade plan immediately with prorated billing
     */
    public function upgradePlan(Subscription $subscription, Plan $newPlan): Subscription
    {
        if (!$newPlan->isUpgradeFrom($subscription->plan)) {
            throw new \Exception('O plano selecionado não é um upgrade.');
        }

        return DB::transaction(function () use ($subscription, $newPlan) {
            // Store previous plan
            $previousPlan = $subscription->plan;

            // Calculate prorated amount using CreditService
            $proratedAmount = $this->creditService->calculateUpgradeProration(
                $subscription,
                $newPlan->price,
                $previousPlan->price
            );

            // Update subscription immediately
            $subscription->update([
                'previous_plan_id' => $previousPlan->id,
                'plan_id' => $newPlan->id,
                'prorated_amount' => $proratedAmount,
                'current_period_start' => Carbon::now(),
                'current_period_end' => $this->calculatePeriodEnd($newPlan),
                'next_billing_date' => $this->calculatePeriodEnd($newPlan),
                'status' => 'active', // Remove trial if exists
                'trial_ends_at' => null,
            ]);

            // Update usage limits
            $this->syncUsageLimits($subscription);

            // Log upgrade
            $this->auditService->logUpgraded($subscription, $previousPlan, $proratedAmount);

            // Record usage snapshot
            SubscriptionUsageHistory::recordSnapshot($subscription);

            return $subscription->fresh();
        });
    }

    /**
     * Downgrade plan (scheduled for next billing period)
     */
    public function downgradePlan(Subscription $subscription, Plan $newPlan): Subscription
    {
        if (!$newPlan->isDowngradeFrom($subscription->plan)) {
            throw new \Exception('O plano selecionado não é um downgrade.');
        }

        return DB::transaction(function () use ($subscription, $newPlan) {
            $currentPlan = $subscription->plan;

            // Schedule the plan change for next billing date
            $subscription->update([
                'scheduled_plan_id' => $newPlan->id,
                'plan_change_scheduled_for' => $subscription->next_billing_date,
            ]);

            // Log downgrade scheduled
            $this->auditService->logDowngradeScheduled($subscription, $currentPlan, $newPlan);

            return $subscription->fresh();
        });
    }

    /**
     * Cancel scheduled plan change
     */
    public function cancelScheduledPlanChange(Subscription $subscription): Subscription
    {
        $scheduledPlan = $subscription->scheduledPlan;

        $subscription->update([
            'scheduled_plan_id' => null,
            'plan_change_scheduled_for' => null,
        ]);

        // Log downgrade cancellation
        if ($scheduledPlan) {
            $this->auditService->logDowngradeCanceled($subscription, $scheduledPlan);
        }

        return $subscription->fresh();
    }

    /**
     * Cancel subscription (ends at period end)
     */
    public function cancelSubscription(Subscription $subscription, bool $immediately = false): Subscription
    {
        return DB::transaction(function () use ($subscription, $immediately) {
            $endsAt = $immediately
                ? Carbon::now()
                : $subscription->current_period_end;

            $subscription->update([
                'status' => $immediately ? 'expired' : 'canceled',
                'canceled_at' => Carbon::now(),
                'ends_at' => $endsAt,
            ]);

            // Calculate and process cancellation credit
            $credit = null;
            $creditAmount = null;

            if ($immediately) {
                $creditAmount = $this->creditService->calculateCancellationCredit($subscription);

                // Log cancellation first to get audit log
                $auditLog = $this->auditService->logCanceled($subscription, true, $creditAmount);

                // Process credit if applicable
                if ($creditAmount > 0) {
                    $credit = $this->creditService->processCancellationCredit(
                        $subscription,
                        $auditLog,
                        true
                    );
                }
            } else {
                // Log cancellation without credit
                $this->auditService->logCanceled($subscription, false, null);
            }

            return $subscription->fresh();
        });
    }

    /**
     * Resume a canceled subscription
     */
    public function resumeSubscription(Subscription $subscription): Subscription
    {
        if ($subscription->status !== 'canceled' || $subscription->hasEnded()) {
            throw new \Exception('Não é possível retomar esta subscrição.');
        }

        $subscription->update([
            'status' => 'active',
            'canceled_at' => null,
            'ends_at' => null,
        ]);

        // Log resume
        $this->auditService->logResumed($subscription);

        return $subscription->fresh();
    }

    /**
     * Renew subscription for next billing period
     */
    public function renewSubscription(Subscription $subscription): Subscription
    {
        return DB::transaction(function () use ($subscription) {
            $previousPlan = null;

            // Check if there's a scheduled plan change
            if ($subscription->hasPlanChangeScheduled()) {
                $previousPlan = $subscription->plan;
                $newPlan = $subscription->scheduledPlan;

                $subscription->update([
                    'previous_plan_id' => $subscription->plan_id,
                    'plan_id' => $newPlan->id,
                    'scheduled_plan_id' => null,
                    'plan_change_scheduled_for' => null,
                    'current_period_start' => Carbon::now(),
                    'current_period_end' => $this->calculatePeriodEnd($newPlan),
                    'next_billing_date' => $this->calculatePeriodEnd($newPlan),
                    'prorated_amount' => null,
                ]);

                // Update usage limits for new plan
                $this->syncUsageLimits($subscription);

                // Log downgrade execution
                $this->auditService->logDowngraded($subscription, $previousPlan);
            } else {
                // Regular renewal
                $subscription->update([
                    'current_period_start' => Carbon::now(),
                    'current_period_end' => $this->calculatePeriodEnd($subscription->plan),
                    'next_billing_date' => $this->calculatePeriodEnd($subscription->plan),
                    'prorated_amount' => null,
                ]);

                // Reset usage for limits that have reset_at
                $this->resetPeriodUsage($subscription);
            }

            // Log renewal
            $this->auditService->logRenewed($subscription, $previousPlan);

            // Record usage snapshot
            SubscriptionUsageHistory::recordSnapshot($subscription);

            return $subscription->fresh();
        });
    }

    /**
     * Convert trial to active subscription
     */
    public function convertTrialToActive(Subscription $subscription): Subscription
    {
        if (!$subscription->onTrial()) {
            throw new \Exception('Esta subscrição não está em trial.');
        }

        $subscription->update([
            'status' => 'active',
            'trial_ends_at' => null,
            'trial_notification_sent' => false,
        ]);

        // Log trial conversion
        $this->auditService->logTrialConverted($subscription);

        return $subscription->fresh();
    }

    /**
     * Calculate prorated amount for upgrade
     */
    private function calculateProratedAmount(Subscription $subscription, Plan $newPlan): float
    {
        $currentPlan = $subscription->plan;

        // If current plan is free, no proration needed
        if ($currentPlan->isFree()) {
            return 0;
        }

        // Calculate remaining days in current period
        $totalDays = $subscription->current_period_start->diffInDays($subscription->current_period_end);
        $remainingDays = Carbon::now()->diffInDays($subscription->current_period_end);

        if ($totalDays == 0 || $remainingDays <= 0) {
            return 0;
        }

        // Calculate unused amount from current plan
        $unusedAmount = ($currentPlan->price / $totalDays) * $remainingDays;

        // Calculate new plan amount for remaining period
        $newPlanAmount = ($newPlan->price / $totalDays) * $remainingDays;

        // Prorated amount to charge
        return max(0, $newPlanAmount - $unusedAmount);
    }

    /**
     * Calculate period end based on billing period
     */
    private function calculatePeriodEnd(Plan $plan): Carbon
    {
        return $plan->billing_period === 'yearly'
            ? Carbon::now()->addYear()
            : Carbon::now()->addMonth();
    }

    /**
     * Initialize usage limits for a new subscription
     */
    private function initializeUsageLimits(Subscription $subscription): void
    {
        $limits = $subscription->plan->limits ?? [];

        foreach ($limits as $feature => $limit) {
            SubscriptionUsage::create([
                'subscription_id' => $subscription->id,
                'feature' => $feature,
                'used' => 0,
                'limit' => $limit,
                'reset_at' => $this->shouldResetMonthly($feature)
                    ? $subscription->current_period_end
                    : null,
            ]);
        }
    }

    /**
     * Sync usage limits when plan changes
     */
    private function syncUsageLimits(Subscription $subscription): void
    {
        $limits = $subscription->plan->limits ?? [];

        foreach ($limits as $feature => $limit) {
            $usage = $subscription->usage()->where('feature', $feature)->first();

            if ($usage) {
                // Update existing limit
                $usage->update([
                    'limit' => $limit,
                    'reset_at' => $this->shouldResetMonthly($feature)
                        ? $subscription->current_period_end
                        : null,
                ]);
            } else {
                // Create new usage record
                SubscriptionUsage::create([
                    'subscription_id' => $subscription->id,
                    'feature' => $feature,
                    'used' => 0,
                    'limit' => $limit,
                    'reset_at' => $this->shouldResetMonthly($feature)
                        ? $subscription->current_period_end
                        : null,
                ]);
            }
        }
    }

    /**
     * Reset usage for features that reset monthly
     */
    private function resetPeriodUsage(Subscription $subscription): void
    {
        $subscription->usage()
            ->whereNotNull('reset_at')
            ->each(function ($usage) use ($subscription) {
                $usage->update([
                    'used' => 0,
                    'reset_at' => $subscription->current_period_end,
                ]);
            });
    }

    /**
     * Check if feature usage should reset monthly
     */
    private function shouldResetMonthly(string $feature): bool
    {
        // Features that reset every billing period
        $resettableFeatures = [
            'invoices_per_month',
            'proposals_per_month',
        ];

        return in_array($feature, $resettableFeatures);
    }

    /**
     * Increment usage for a feature
     */
    public function incrementUsage(Subscription $subscription, string $feature, int $amount = 1): bool
    {
        $usage = $subscription->usage()->where('feature', $feature)->first();

        if (!$usage) {
            return true; // No limit set, allow
        }

        // Check if needs reset
        if ($usage->needsReset()) {
            $usage->reset();
        }

        // Check if limit reached
        if ($usage->hasReachedLimit()) {
            return false;
        }

        return $usage->increment($amount);
    }

    /**
     * Decrement usage for a feature
     */
    public function decrementUsage(Subscription $subscription, string $feature, int $amount = 1): bool
    {
        $usage = $subscription->usage()->where('feature', $feature)->first();

        if (!$usage) {
            return true; // No limit set
        }

        return $usage->decrement($amount);
    }

    /**
     * Check if subscription can perform action based on limits
     */
    public function canPerformAction(Subscription $subscription, string $feature): bool
    {
        $usage = $subscription->usage()->where('feature', $feature)->first();

        if (!$usage) {
            return true; // No limit set, allow
        }

        // Check if needs reset
        if ($usage->needsReset()) {
            $usage->reset();
        }

        return !$usage->hasReachedLimit();
    }

    /**
     * Get subscription usage summary
     */
    public function getUsageSummary(Subscription $subscription): array
    {
        $usage = $subscription->usage()->get();

        return $usage->map(function ($item) {
            return [
                'feature' => $item->feature,
                'used' => $item->used,
                'limit' => $item->limit,
                'remaining' => $item->getRemaining(),
                'percentage' => $item->getPercentage(),
                'has_reached_limit' => $item->hasReachedLimit(),
            ];
        })->toArray();
    }
}
