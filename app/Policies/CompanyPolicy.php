<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Users can view companies they have access to
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Company $company): bool
    {
        return $user->hasAccessTo($company->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create a company
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Company $company): bool
    {
        // Only the owner can update the company
        return $user->isOwnerOf($company->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        // Only the owner can delete the company
        return $user->isOwnerOf($company->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Company $company): bool
    {
        return $user->isOwnerOf($company->id);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Company $company): bool
    {
        return $user->isOwnerOf($company->id);
    }

    /**
     * Determine whether the user can switch to this company.
     */
    public function switch(User $user, Company $company): bool
    {
        return $user->hasAccessTo($company->id);
    }

    /**
     * Determine whether the user can manage users for this company.
     */
    public function manageUsers(User $user, Company $company): bool
    {
        return $user->isOwnerOf($company->id);
    }
}
