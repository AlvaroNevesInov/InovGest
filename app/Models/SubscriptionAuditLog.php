<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'tenant_id',
        'user_id',
        'event',
        'previous_plan_id',
        'new_plan_id',
        'previous_status',
        'new_status',
        'amount',
        'prorated_amount',
        'credit_amount',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2',
        'prorated_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    /**
     * Eventos disponíveis para logging
     */
    const EVENT_CREATED = 'created';
    const EVENT_UPGRADED = 'upgraded';
    const EVENT_DOWNGRADED = 'downgraded';
    const EVENT_DOWNGRADE_SCHEDULED = 'downgrade_scheduled';
    const EVENT_DOWNGRADE_CANCELED = 'downgrade_canceled';
    const EVENT_CANCELED = 'canceled';
    const EVENT_CANCELED_IMMEDIATELY = 'canceled_immediately';
    const EVENT_RESUMED = 'resumed';
    const EVENT_RENEWED = 'renewed';
    const EVENT_TRIAL_STARTED = 'trial_started';
    const EVENT_TRIAL_CONVERTED = 'trial_converted';
    const EVENT_TRIAL_EXPIRED = 'trial_expired';
    const EVENT_EXPIRED = 'expired';

    /**
     * Relacionamentos
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function previousPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'previous_plan_id');
    }

    public function newPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'new_plan_id');
    }

    /**
     * Scopes
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForSubscription($query, $subscriptionId)
    {
        return $query->where('subscription_id', $subscriptionId);
    }

    public function scopeByEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Helper methods
     */
    public function getEventLabel(): string
    {
        return match($this->event) {
            self::EVENT_CREATED => 'Subscrição Criada',
            self::EVENT_UPGRADED => 'Upgrade de Plano',
            self::EVENT_DOWNGRADED => 'Downgrade de Plano',
            self::EVENT_DOWNGRADE_SCHEDULED => 'Downgrade Agendado',
            self::EVENT_DOWNGRADE_CANCELED => 'Downgrade Cancelado',
            self::EVENT_CANCELED => 'Subscrição Cancelada',
            self::EVENT_CANCELED_IMMEDIATELY => 'Subscrição Cancelada Imediatamente',
            self::EVENT_RESUMED => 'Subscrição Retomada',
            self::EVENT_RENEWED => 'Subscrição Renovada',
            self::EVENT_TRIAL_STARTED => 'Período de Teste Iniciado',
            self::EVENT_TRIAL_CONVERTED => 'Teste Convertido em Ativo',
            self::EVENT_TRIAL_EXPIRED => 'Período de Teste Expirado',
            self::EVENT_EXPIRED => 'Subscrição Expirada',
            default => ucfirst(str_replace('_', ' ', $this->event)),
        };
    }

    public function isFinancialEvent(): bool
    {
        return in_array($this->event, [
            self::EVENT_CREATED,
            self::EVENT_UPGRADED,
            self::EVENT_DOWNGRADED,
            self::EVENT_CANCELED,
            self::EVENT_RENEWED,
        ]);
    }
}
