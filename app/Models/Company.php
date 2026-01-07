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
        'notes',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
