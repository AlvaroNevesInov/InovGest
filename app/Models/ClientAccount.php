<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientAccount extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'entity_id',
        'movement_date',
        'type',
        'document_type',
        'document_id',
        'document_number',
        'description',
        'amount',
        'balance',
        'notes',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
