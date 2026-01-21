<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionUsage extends Model
{
    protected $table = 'subscription_usage';

    protected $fillable = [
        'subscription_id',
        'feature',
        'used',
        'limit',
        'reset_at',
    ];

    protected $casts = [
        'used' => 'integer',
        'limit' => 'integer',
        'reset_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Check if limit has been reached
     */
    public function hasReachedLimit(): bool
    {
        if ($this->limit === null) {
            return false; // Unlimited
        }

        return $this->used >= $this->limit;
    }

    /**
     * Check if usage needs to be reset
     */
    public function needsReset(): bool
    {
        return $this->reset_at && $this->reset_at->isPast();
    }

    /**
     * Get remaining usage
     */
    public function getRemaining(): ?int
    {
        if ($this->limit === null) {
            return null; // Unlimited
        }

        return max(0, $this->limit - $this->used);
    }

    /**
     * Get usage percentage
     */
    public function getPercentage(): ?float
    {
        if ($this->limit === null || $this->limit === 0) {
            return null; // Unlimited or invalid
        }

        return round(($this->used / $this->limit) * 100, 2);
    }

    /**
     * Increment usage
     */
    public function increment(int $amount = 1): bool
    {
        if ($this->hasReachedLimit()) {
            return false;
        }

        $this->used += $amount;
        return $this->save();
    }

    /**
     * Decrement usage
     */
    public function decrement(int $amount = 1): bool
    {
        $this->used = max(0, $this->used - $amount);
        return $this->save();
    }

    /**
     * Reset usage
     */
    public function reset(): bool
    {
        $this->used = 0;
        $this->reset_at = null;
        return $this->save();
    }
}
