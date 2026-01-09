<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierOrderLine extends Model
{
    protected $fillable = [
        'supplier_order_id',
        'article_id',
        'quantity',
        'unit_price',
        'tax_rate',
        'subtotal',
        'tax_amount',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (SupplierOrderLine $line) {
            $line->subtotal = $line->quantity * $line->unit_price;
            $line->tax_amount = $line->subtotal * ($line->tax_rate / 100);
            $line->total = $line->subtotal + $line->tax_amount;
        });

        static::saved(function (SupplierOrderLine $line) {
            $line->supplierOrder->calculateTotals();
        });

        static::deleted(function (SupplierOrderLine $line) {
            $line->supplierOrder->calculateTotals();
        });
    }

    /**
     * Get the supplier order that owns the line.
     */
    public function supplierOrder(): BelongsTo
    {
        return $this->belongsTo(SupplierOrder::class);
    }

    /**
     * Get the article for this line.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
