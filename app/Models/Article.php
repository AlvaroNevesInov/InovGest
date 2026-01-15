<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'reference',
        'name',
        'description',
        'price',
        'vat_rate_id',
        'photo',
        'notes',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    /**
     * Get the VAT rate that owns the article.
     */
    public function vatRate(): BelongsTo
    {
        return $this->belongsTo(VatRate::class);
    }

    /**
     * Scope a query to only include active articles.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get the price with VAT included.
     */
    public function getPriceWithVatAttribute(): float
    {
        if ($this->vatRate) {
            return $this->price * (1 + ($this->vatRate->rate / 100));
        }
        return $this->price;
    }

    /**
     * Get the photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}
