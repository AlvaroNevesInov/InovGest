<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'article_id',
        'article_reference',
        'article_name',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'discount_percentage',
        'vat_rate_id',
        'vat_rate',
        'subtotal',
        'tax_amount',
        'total',
        'supplier_id',
        'cost_price',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($line) {
            if ($line->article_id) {
                $article = Article::find($line->article_id);
                if ($article) {
                    $line->article_reference = $article->reference;
                    $line->article_name = $line->article_name ?? $article->name;
                    $line->description = $line->description ?? $article->description;
                    $line->unit_price = $line->unit_price ?? $article->price;
                    $line->vat_rate_id = $line->vat_rate_id ?? $article->vat_rate_id;
                    $line->vat_rate = $article->vatRate->rate;
                }
            }

            if ($line->vat_rate_id && !$line->vat_rate) {
                $vatRate = VatRate::find($line->vat_rate_id);
                if ($vatRate) {
                    $line->vat_rate = $vatRate->rate;
                }
            }

            $line->calculateTotals();
        });

        static::updating(function ($line) {
            $line->calculateTotals();
        });

        static::saved(function ($line) {
            if ($line->proposal) {
                $line->proposal->calculateTotals();
            }
        });

        static::deleted(function ($line) {
            if ($line->proposal) {
                $line->proposal->calculateTotals();
            }
        });
    }

    /**
     * Get the proposal.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the article.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the VAT rate.
     */
    public function vatRate(): BelongsTo
    {
        return $this->belongsTo(VatRate::class);
    }

    /**
     * Get the supplier entity.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }

    /**
     * Calculate line totals.
     */
    public function calculateTotals(): void
    {
        // Calculate subtotal before discount
        $subtotalBeforeDiscount = $this->quantity * $this->unit_price;

        // Apply discount
        if ($this->discount_percentage > 0) {
            $this->discount = $subtotalBeforeDiscount * ($this->discount_percentage / 100);
        }

        // Calculate subtotal after discount
        $this->subtotal = $subtotalBeforeDiscount - $this->discount;

        // Calculate tax
        $this->tax_amount = $this->subtotal * ($this->vat_rate / 100);

        // Calculate total
        $this->total = $this->subtotal + $this->tax_amount;
    }

    /**
     * Get the profit margin.
     */
    public function getProfitMarginAttribute(): ?float
    {
        if (!$this->cost_price || $this->cost_price == 0) {
            return null;
        }

        $totalCost = $this->cost_price * $this->quantity;
        $profit = $this->subtotal - $totalCost;

        return ($profit / $totalCost) * 100;
    }

    /**
     * Get the profit amount.
     */
    public function getProfitAmountAttribute(): ?float
    {
        if (!$this->cost_price) {
            return null;
        }

        $totalCost = $this->cost_price * $this->quantity;
        return $this->subtotal - $totalCost;
    }
}
