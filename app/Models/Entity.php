<?php

namespace App\Models;

use App\Models\Concerns\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use HasFactory, HasEncryptedAttributes;

    protected $fillable = [
        'type',
        'number',
        'nif',
        'name',
        'address',
        'postal_code',
        'city',
        'country_id',
        'phone',
        'mobile',
        'website',
        'email',
        'rgpd_consent',
        'notes',
        'active',
    ];

    protected $casts = [
        'rgpd_consent' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * Get the list of attributes that should be encrypted.
     * Note: NIF is not encrypted to maintain unique constraint validation
     */
    protected function getEncryptedAttributes(): array
    {
        return ['email', 'phone', 'mobile'];
    }

    /**
     * Get the decrypted email.
     */
    protected function email(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => $value ? $this->decryptValue($value) : null,
            set: fn ($value) => $value ? \Illuminate\Support\Facades\Crypt::encryptString($value) : null,
        );
    }

    /**
     * Get the decrypted phone.
     */
    protected function phone(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => $value ? $this->decryptValue($value) : null,
            set: fn ($value) => $value ? \Illuminate\Support\Facades\Crypt::encryptString($value) : null,
        );
    }

    /**
     * Get the decrypted mobile.
     */
    protected function mobile(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => $value ? $this->decryptValue($value) : null,
            set: fn ($value) => $value ? \Illuminate\Support\Facades\Crypt::encryptString($value) : null,
        );
    }

    /**
     * Helper to decrypt a value safely.
     */
    private function decryptValue($value)
    {
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function scopeClients($query)
    {
        return $query->whereIn('type', ['client', 'both']);
    }

    public function scopeSuppliers($query)
    {
        return $query->whereIn('type', ['supplier', 'both']);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
