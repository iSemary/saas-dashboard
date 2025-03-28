<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\FileManager\Traits\FileHandler;
use Modules\Tenant\Entities\Tenant;
use Modules\Utilities\Entities\Currency;
use OwenIt\Auditing\Contracts\Auditable;

class Subscription extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, FileHandler;

    protected $connection = 'landlord';

    public $singleTitle = "subscription";
    public $pluralTitle = "subscriptions";

    protected $fillable = [
        'tenant_id',
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'price',
        'currency_id',
        'canceled_at',
        'cancellation_reason',
    ];

    /**
     * Get the tenant associated with the subscription
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the user associated with the subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the plan associated with the subscription
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * Get the currency for the subscription
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * Scope a query to only include active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope a query to only include canceled subscriptions
     */
    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    /**
     * Check if the subscription is currently active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' &&
            now()->between($this->start_date, $this->end_date);
    }

    /**
     * Check if the subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
            now()->greaterThan($this->end_date);
    }

    /**
     * Check if the subscription is canceled
     */
    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }
}
