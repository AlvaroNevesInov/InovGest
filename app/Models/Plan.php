<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_period',
        'trial_days',
        'features',
        'limits',
        'is_active',
        'is_popular',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'trial_days' => 'integer',
        'features' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    /**
     * Get limit for a specific feature
     */
    public function getLimit(string $feature): ?int
    {
        return $this->limits[$feature] ?? null;
    }

    /**
     * Check if plan has a specific feature
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Check if this is a free plan
     */
    public function isFree(): bool
    {
        return $this->price == 0;
    }

    /**
     * Get monthly price (convert yearly to monthly for comparison)
     */
    public function getMonthlyPriceAttribute(): float
    {
        if ($this->billing_period === 'yearly') {
            return round($this->price / 12, 2);
        }
        return $this->price;
    }

    /**
     * Check if this plan is an upgrade from another plan
     */
    public function isUpgradeFrom(Plan $otherPlan): bool
    {
        return $this->price > $otherPlan->price;
    }

    /**
     * Check if this plan is a downgrade from another plan
     */
    public function isDowngradeFrom(Plan $otherPlan): bool
    {
        return $this->price < $otherPlan->price;
    }
}
