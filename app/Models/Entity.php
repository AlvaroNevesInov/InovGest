<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Entity extends Model
{
    use HasFactory, LogsActivity, BelongsToCompany;

    protected $fillable = [
        'company_id',
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'nif', 'type', 'email', 'phone', 'active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
