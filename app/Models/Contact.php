<?php

namespace App\Models;

use App\Models\Concerns\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory, HasEncryptedAttributes;

    protected $fillable = [
        'number',
        'entity_id',
        'name',
        'surname',
        'contact_function_id',
        'phone',
        'mobile',
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
     */
    protected function getEncryptedAttributes(): array
    {
        return ['email', 'phone', 'mobile'];
    }

    /**
     * Get the entity that owns the contact.
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get the contact function.
     */
    public function contactFunction(): BelongsTo
    {
        return $this->belongsTo(ContactFunction::class);
    }

    /**
     * Scope a query to only include active contacts.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->surname);
    }
}
