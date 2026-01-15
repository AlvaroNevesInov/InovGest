<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'nif',
        'address',
        'postal_code',
        'city',
        'country_id',
        'phone',
        'mobile',
        'email',
        'website',
        'logo_path',
        'notes',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the users that have access to this company.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_owner')
            ->withTimestamps();
    }

    /**
     * Get the users that own this company.
     */
    public function owners()
    {
        return $this->users()->wherePivot('is_owner', true);
    }
}
