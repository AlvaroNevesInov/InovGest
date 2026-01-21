<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Subscription extends Model
{
    protected $fillable = [
        'tenant_id',
        'plan_id',
        'previous_plan_id',
        'status',
        'trial_ends_at',
        'trial_notification_sent',
        'current_period_start',
        'current_period_end',
        'next_billing_date',
        'scheduled_plan_id',
        'plan_change_scheduled_for',
        'prorated_amount',
        'canceled_at',
        'ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'trial_notification_sent' => 'boolean',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'next_billing_date' => 'datetime',
        'plan_change_scheduled_for' => 'datetime',
        'prorated_amount' => 'decimal:2',
        'canceled_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function previousPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'previous_plan_id');
    }

    public function scheduledPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'scheduled_plan_id');
    }

    public function usage(): HasMany
    {
        return $this->hasMany(SubscriptionUsage::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTrialing($query)
    {
        return $query->where('status', 'trialing');
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Check if subscription is on trial
     */
    public function onTrial(): bool
    {
        return $this->status === 'trialing' &&
               $this->trial_ends_at &&
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if trial is ending soon (within 3 days)
     */
    public function trialEndingSoon(): bool
    {
        if (!$this->onTrial()) {
            return false;
        }

        return $this->trial_ends_at->diffInDays(now()) <= 3;
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trialing']);
    }

    /**
     * Check if subscription is canceled but still active
     */
    public function isCanceledButActive(): bool
    {
        return $this->status === 'canceled' &&
               $this->ends_at &&
               $this->ends_at->isFuture();
    }

    /**
     * Check if subscription has ended
     */
    public function hasEnded(): bool
    {
        return $this->status === 'expired' ||
               ($this->ends_at && $this->ends_at->isPast());
    }

    /**
     * Check if plan change is scheduled
     */
    public function hasPlanChangeScheduled(): bool
    {
        return $this->scheduled_plan_id !== null;
    }

    /**
     * Get days remaining in trial
     */
    public function daysRemainingInTrial(): int
    {
        if (!$this->onTrial()) {
            return 0;
        }

        return max(0, $this->trial_ends_at->diffInDays(now()));
    }

    /**
     * Get days remaining in current period
     */
    public function daysRemainingInPeriod(): int
    {
        if (!$this->current_period_end) {
            return 0;
        }

        return max(0, $this->current_period_end->diffInDays(now()));
    }

    /**
     * Check if can upgrade/downgrade
     */
    public function canChangePlan(): bool
    {
        return $this->isActive() && !$this->hasEnded();
    }

    /**
     * Check usage limit for a feature
     */
    public function hasReachedLimit(string $feature): bool
    {
        $usage = $this->usage()->where('feature', $feature)->first();

        if (!$usage || $usage->limit === null) {
            return false; // No limit or unlimited
        }

        return $usage->used >= $usage->limit;
    }

    /**
     * Get usage for a feature
     */
    public function getUsage(string $feature): ?SubscriptionUsage
    {
        return $this->usage()->where('feature', $feature)->first();
    }

    /**
     * Get remaining usage for a feature
     */
    public function getRemainingUsage(string $feature): ?int
    {
        $usage = $this->getUsage($feature);

        if (!$usage || $usage->limit === null) {
            return null; // Unlimited
        }

        return max(0, $usage->limit - $usage->used);
    }
}
