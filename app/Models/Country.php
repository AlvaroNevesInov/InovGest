<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function entities()
    {
        return $this->hasMany(Entity::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
