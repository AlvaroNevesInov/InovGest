<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SupplierOrder extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'number',
        'order_date',
        'supplier_id',
        'order_id',
        'subtotal',
        'tax_total',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the supplier (entity) that owns the supplier order.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }

    /**
     * Get the original order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the lines for the supplier order.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(SupplierOrderLine::class);
    }

    /**
     * Calculate and update totals.
     */
    public function calculateTotals(): void
    {
        $this->subtotal = $this->lines->sum('subtotal');
        $this->tax_total = $this->lines->sum('tax_amount');
        $this->total = $this->lines->sum('total');
        $this->save();
    }

    /**
     * Scope a query to only include orders with a specific status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['number', 'status', 'total', 'supplier_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
