<?php

namespace App\Models;

use App\Models\Concerns\HasDocuments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory, HasDocuments;

    protected $fillable = [
        'number',
        'order_date',
        'entity_id',
        'proposal_id',
        'status',
        'subtotal',
        'tax_total',
        'total',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->number) {
                $order->number = static::getNextNumber();
            }

            if (!$order->order_date) {
                $order->order_date = now();
            }
        });
    }

    /**
     * Get the next order number.
     */
    public static function getNextNumber(): int
    {
        return (static::max('number') ?? 0) + 1;
    }

    /**
     * Get the client entity.
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get the client (alias for entity).
     */
    public function client(): BelongsTo
    {
        return $this->entity();
    }

    /**
     * Get the original proposal.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the order lines.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class)->orderBy('sort_order');
    }

    /**
     * Calculate and update totals.
     */
    public function calculateTotals(): void
    {
        $this->lines->each->calculateTotals();

        $this->subtotal = $this->lines->sum('subtotal');
        $this->tax_total = $this->lines->sum('tax_amount');
        $this->total = $this->lines->sum('total');
        $this->save();
    }

    /**
     * Check if order is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if order is closed.
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Close the order.
     */
    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    /**
     * Scope a query to only include draft orders.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include closed orders.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }
}
