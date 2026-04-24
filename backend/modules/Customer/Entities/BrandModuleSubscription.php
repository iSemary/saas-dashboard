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

    protected $fillable = [
        'brand_id',
        'module_key',
        'module_name',
        'subscription_status',
        'subscription_start',
        'subscription_end',
        'module_config',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'subscription_start' => 'datetime',
        'subscription_end' => 'datetime',
        'module_config' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $translatable = [
        'module_name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) 
        {
            if (auth()->check()) 
            {
                $subscription->created_by = auth()->id();
            }
            if (empty($subscription->subscription_start)) 
            {
                $subscription->subscription_start = now();
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
        return $query->where('subscription_status', 'active');
    }

    /**
     * Scope for checking if subscription is active and not expired.
     */
    public function scopeValidSubscription($query)
    {
        return $query->where('subscription_status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('subscription_end')
                          ->orWhere('subscription_end', '>', now());
                    });
    }

    /**
     * Check if subscription is valid.
     */
    public function isValidSubscription()
    {
        return $this->subscription_status === 'active' && 
               ($this->subscription_end === null || $this->subscription_end > now());
    }

    /**
     * Get subscription badge class for UI.
     */
    public function getStatusBadgeClass()
    {
        return match($this->subscription_status) 
        {
            'active' => 'badge-success',
            'inactive' => 'badge-warning',
            'suspended' => 'badge-info',
            'expired' => 'badge-danger',
            default => 'badge-secondary'
        };
    }
}
