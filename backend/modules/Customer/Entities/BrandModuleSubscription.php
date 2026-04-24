<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\Localization\Traits\Translatable;
use OwenIt\Auditing\Contracts\Auditable;

class BrandModuleSubscription extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Translatable, \OwenIt\Auditing\Auditable;

    protected $table = 'brand_modules';

    protected $fillable = [
        'brand_id',
        'module_id',
        'module_key',
        'status',
        'color_palette',
        'module_config',
        'subscribed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'module_config' => 'array',
        'color_palette' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $translatable = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription)
        {
            if (auth()->check())
            {
                $subscription->created_by = auth()->id();
            }
            if (empty($subscription->subscribed_at))
            {
                $subscription->subscribed_at = now();
            }
        });

        static::updating(function ($subscription)
        {
            if (auth()->check())
            {
                $subscription->updated_by = auth()->id();
            }
        });
    }

    /**
     * Get the brand that owns the subscription.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the user who created the subscription.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the subscription.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for checking if subscription is active.
     */
    public function scopeValidSubscription($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if subscription is valid.
     */
    public function isValidSubscription()
    {
        return $this->status === 'active';
    }

    /**
     * Get subscription badge class for UI.
     */
    public function getStatusBadgeClass()
    {
        return match($this->status)
        {
            'active' => 'badge-success',
            'inactive' => 'badge-warning',
            'suspended' => 'badge-info',
            'expired' => 'badge-danger',
            default => 'badge-secondary'
        };
    }
}
