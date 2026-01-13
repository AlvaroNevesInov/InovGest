<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SupplierInvoice extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'number',
        'invoice_date',
        'due_date',
        'supplier_id',
        'supplier_order_id',
        'total_amount',
        'document_path',
        'payment_proof_path',
        'status',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }

    public function supplierOrder(): BelongsTo
    {
        return $this->belongsTo(SupplierOrder::class);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['number', 'status', 'total_amount', 'supplier_id', 'payment_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
