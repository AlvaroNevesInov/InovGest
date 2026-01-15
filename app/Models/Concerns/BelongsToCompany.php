<?php

namespace App\Models\Concerns;

use App\Models\Company;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    /**
     * Boot the trait and add global scope.
     */
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        // Automatically set company_id when creating a model
        static::creating(function ($model) {
            if (!$model->company_id && app()->has('current_company_id')) {
                $model->company_id = app('current_company_id');
            }
        });
    }

    /**
     * Get the company that owns the model.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope to ignore company filter (use with caution).
     */
    public function scopeWithoutCompanyScope($query)
    {
        return $query->withoutGlobalScope(CompanyScope::class);
    }

    /**
     * Scope to filter by specific company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->withoutGlobalScope(CompanyScope::class)->where('company_id', $companyId);
    }
}
