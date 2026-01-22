<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'audit_log_id',
        'amount',
        'balance_before',
        'balance_after',
        'type',
        'status',
        'description',
        'metadata',
        'expires_at',
        'applied_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'applied_at' => 'datetime',
    ];

    /**
     * Tipos de crédito
     */
    const TYPE_REFUND = 'refund';
    const TYPE_CANCELLATION_CREDIT = 'cancellation_credit';
    const TYPE_PROMOTIONAL = 'promotional';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_USAGE = 'usage';

    /**
     * Status
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPLIED = 'applied';
    const STATUS_EXPIRED = 'expired';
    const STATUS_VOID = 'void';

    /**
     * Relacionamentos
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function auditLog(): BelongsTo
    {
        return $this->belongsTo(SubscriptionAuditLog::class);
    }

    /**
     * Scopes
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_PENDING)
            ->where(function($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_PENDING)
            ->where('expires_at', '<=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Helper methods
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_PENDING
            && (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function markAsApplied(): void
    {
        $this->update([
            'status' => self::STATUS_APPLIED,
            'applied_at' => now(),
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update([
            'status' => self::STATUS_EXPIRED,
        ]);
    }

    public function markAsVoid(): void
    {
        $this->update([
            'status' => self::STATUS_VOID,
        ]);
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_REFUND => 'Reembolso',
            self::TYPE_CANCELLATION_CREDIT => 'Crédito de Cancelamento',
            self::TYPE_PROMOTIONAL => 'Crédito Promocional',
            self::TYPE_ADJUSTMENT => 'Ajuste',
            self::TYPE_USAGE => 'Utilização de Crédito',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_APPLIED => 'Aplicado',
            self::STATUS_EXPIRED => 'Expirado',
            self::STATUS_VOID => 'Anulado',
            default => ucfirst($this->status),
        };
    }
}
