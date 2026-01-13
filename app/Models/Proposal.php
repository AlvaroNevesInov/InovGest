<?php

namespace App\Models;

use App\Models\Concerns\HasDocuments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Proposal extends Model
{
    use HasFactory, HasDocuments, LogsActivity;

    protected $fillable = [
        'number',
        'proposal_date',
        'entity_id',
        'validity_date',
        'status',
        'subtotal',
        'tax_total',
        'total',
        'notes',
    ];

    protected $casts = [
        'proposal_date' => 'date',
        'validity_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($proposal) {
            if (!$proposal->number) {
                $proposal->number = static::getNextNumber();
            }

            if (!$proposal->proposal_date) {
                $proposal->proposal_date = now();
            }

            if (!$proposal->validity_date) {
                $proposal->validity_date = Carbon::parse($proposal->proposal_date)->addDays(30);
            }
        });
    }

    /**
     * Get the next proposal number.
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
     * Get the proposal lines.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(ProposalLine::class)->orderBy('sort_order');
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
     * Check if proposal is expired.
     */
    public function isExpired(): bool
    {
        return $this->validity_date < now();
    }

    /**
     * Check if proposal is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if proposal is closed.
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Close the proposal.
     */
    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    /**
     * Scope a query to only include draft proposals.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include closed proposals.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope a query to only include expired proposals.
     */
    public function scopeExpired($query)
    {
        return $query->where('validity_date', '<', now());
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['number', 'status', 'total', 'entity_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
