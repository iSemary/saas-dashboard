<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Utilities\Entities\Currency;
use OwenIt\Auditing\Contracts\Auditable;

class PlanPrice extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    protected $fillable = [
        'plan_id',
        'currency_id',
        'country_code',
        'price',
        'setup_fee',
        'billing_cycle',
        'billing_interval',
        'valid_from',
        'valid_until',
        'metadata',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'billing_interval' => 'integer',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'metadata' => 'array',
    ];

    /**
     * Get the plan that owns the price.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the currency for the price.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Scope for active prices.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for valid prices (within date range).
     */
    public function scopeValid($query, $date = null)
    {
        $date = $date ?: now();
        
        return $query->where(function ($query) use ($date) {
            $query->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', $date);
        })->where(function ($query) use ($date) {
            $query->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', $date);
        });
    }

    /**
     * Scope for specific billing cycle.
     */
    public function scopeBillingCycle($query, $cycle)
    {
        return $query->where('billing_cycle', $cycle);
    }

    /**
     * Scope for specific country.
     */
    public function scopeForCountry($query, $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }

    /**
     * Scope for global pricing (no country restriction).
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('country_code');
    }

    /**
     * Get formatted price with currency symbol.
     */
    public function getFormattedPriceAttribute()
    {
        return $this->currency->symbol . number_format($this->price, 2);
    }

    /**
     * Get total price including setup fee.
     */
    public function getTotalPriceAttribute()
    {
        return $this->price + $this->setup_fee;
    }

    /**
     * Check if price is currently valid.
     */
    public function isValid($date = null)
    {
        $date = $date ?: now();
        
        if ($this->valid_from && $date->lt($this->valid_from)) {
            return false;
        }
        
        if ($this->valid_until && $date->gt($this->valid_until)) {
            return false;
        }
        
        return $this->status === 'active';
    }

    /**
     * Calculate price for multiple billing cycles.
     */
    public function calculatePrice($cycles = 1)
    {
        return $this->price * $cycles;
    }

    /**
     * Get billing cycle in days.
     */
    public function getBillingCycleDays()
    {
        $days = [
            'monthly' => 30,
            'quarterly' => 90,
            'semi_annually' => 180,
            'annually' => 365,
            'biennially' => 730,
            'triennially' => 1095,
            'lifetime' => 36500, // 100 years
        ];

        return ($days[$this->billing_cycle] ?? 30) * $this->billing_interval;
    }
}
