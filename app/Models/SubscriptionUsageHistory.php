<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionUsageHistory extends Model
{
    use HasFactory;

    protected $table = 'subscription_usage_history';

    protected $fillable = [
        'subscription_id',
        'tenant_id',
        'feature',
        'used',
        'limit',
        'percentage',
        'recorded_date',
    ];

    protected $casts = [
        'used' => 'integer',
        'limit' => 'integer',
        'percentage' => 'decimal:2',
        'recorded_date' => 'date',
    ];

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

    public function scopeForFeature($query, $feature)
    {
        return $query->where('feature', $feature);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('recorded_date', [$startDate, $endDate]);
    }

    public function scopeLastDays($query, $days = 30)
    {
        return $query->where('recorded_date', '>=', now()->subDays($days)->toDateString());
    }

    /**
     * Helper methods
     */
    public static function recordSnapshot(Subscription $subscription): void
    {
        $today = now()->toDateString();

        foreach ($subscription->usage as $usage) {
            self::updateOrCreate(
                [
                    'subscription_id' => $subscription->id,
                    'tenant_id' => $subscription->tenant_id,
                    'feature' => $usage->feature,
                    'recorded_date' => $today,
                ],
                [
                    'used' => $usage->used,
                    'limit' => $usage->limit,
                    'percentage' => $usage->getPercentage(),
                ]
            );
        }
    }

    /**
     * Obter dados para gráficos
     */
    public static function getChartData(int $subscriptionId, string $feature, int $days = 30): array
    {
        $records = self::forSubscription($subscriptionId)
            ->forFeature($feature)
            ->lastDays($days)
            ->orderBy('recorded_date')
            ->get();

        return [
            'labels' => $records->pluck('recorded_date')->map(fn($date) => $date->format('d/m'))->toArray(),
            'used' => $records->pluck('used')->toArray(),
            'limit' => $records->pluck('limit')->toArray(),
            'percentage' => $records->pluck('percentage')->toArray(),
        ];
    }
}
