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
}
