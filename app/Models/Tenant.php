<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'owner_id',
        'settings',
        'active',
    ];

    protected $casts = [
        'settings' => 'array',
        'active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name);
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tenant')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function onboardingChecklists(): HasMany
    {
        return $this->hasMany(OnboardingChecklist::class);
    }

    public function subscription(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->whereIn('status', ['active', 'trialing'])
            ->latest();
    }

    /**
     * Get the onboarding completion percentage.
     */
    public function getOnboardingCompletionPercentage(): int
    {
        return OnboardingChecklist::getCompletionPercentage($this->id);
    }

    /**
     * Check if onboarding is complete.
     */
    public function isOnboardingComplete(): bool
    {
        return OnboardingChecklist::allRequiredTasksCompleted($this->id);
    }

    /**
     * Check if tenant has an active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Check if tenant is on trial
     */
    public function onTrial(): bool
    {
        $subscription = $this->activeSubscription;
        return $subscription && $subscription->onTrial();
    }

    /**
     * Check if tenant can access a feature
     */
    public function canAccessFeature(string $feature): bool
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return false;
        }

        return $subscription->plan->hasFeature($feature);
    }

    /**
     * Check if tenant has reached limit for a feature
     */
    public function hasReachedLimit(string $feature): bool
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return true;
        }

        return $subscription->hasReachedLimit($feature);
    }
}
