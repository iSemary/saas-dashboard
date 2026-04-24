<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class PlanDiscount extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    protected $fillable = [
        'plan_id', 'name', 'code', 'description', 'discount_type', 'discount_value',
        'applies_to', 'cycle_count', 'usage_limit', 'usage_limit_per_customer',
        'usage_count', 'minimum_amount', 'applicable_countries', 'applicable_currencies',
        'start_date', 'end_date', 'is_stackable', 'metadata', 'status'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'cycle_count' => 'integer',
        'usage_limit' => 'integer',
        'usage_limit_per_customer' => 'integer',
        'usage_count' => 'integer',
        'minimum_amount' => 'decimal:2',
        'applicable_countries' => 'array',
        'applicable_currencies' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_stackable' => 'boolean',
        'metadata' => 'array',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                    });
    }

    public function isValid($countryCode = null, $currencyCode = null)
    {
        if ($this->status !== 'active') return false;
        if ($this->start_date && now()->lt($this->start_date)) return false;
        if ($this->end_date && now()->gt($this->end_date)) return false;
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) return false;
        
        if ($countryCode && $this->applicable_countries && !in_array($countryCode, $this->applicable_countries)) {
            return false;
        }
        
        if ($currencyCode && $this->applicable_currencies && !in_array($currencyCode, $this->applicable_currencies)) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if ($this->minimum_amount && $amount < $this->minimum_amount) {
            return 0;
        }

        switch ($this->discount_type) {
            case 'percentage':
                return $amount * ($this->discount_value / 100);
            case 'fixed':
                return min($this->discount_value, $amount);
            default:
                return 0;
        }
    }
}
