<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Get the companies that the user has access to.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class)
            ->withPivot('is_owner')
            ->withTimestamps();
    }

    /**
     * Get the user's current active company.
     */
    public function currentCompany()
    {
        return $this->belongsTo(Company::class, 'current_company_id');
    }

    /**
     * Check if the user is the owner of a specific company.
     */
    public function isOwnerOf($companyId): bool
    {
        return $this->companies()
            ->wherePivot('company_id', $companyId)
            ->wherePivot('is_owner', true)
            ->exists();
    }

    /**
     * Check if the user has access to a specific company.
     */
    public function hasAccessTo($companyId): bool
    {
        return $this->companies()
            ->where('companies.id', $companyId)
            ->exists();
    }

    /**
     * Switch the user's current active company.
     */
    public function switchCompany($companyId): bool
    {
        if ($this->hasAccessTo($companyId)) {
            $this->current_company_id = $companyId;
            return $this->save();
        }

        return false;
    }
}
