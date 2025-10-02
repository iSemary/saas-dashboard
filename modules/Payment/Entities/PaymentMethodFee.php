<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Modules\Utilities\Entities\Currency;

class PaymentMethodFee extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "payment method fee";
    public $pluralTitle = "payment method fees";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_method_id',
        'currency_id',
        'fee_type',
        'fee_value',
        'min_fee',
        'max_fee',
        'fee_tiers',
        'fixed_fee',
        'applies_to',
        'region',
        'customer_segment',
        'status',
        'effective_from',
        'effective_until',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fee_value' => 'decimal:4',
        'min_fee' => 'decimal:2',
        'max_fee' => 'decimal:2',
        'fee_tiers' => 'array',
        'fixed_fee' => 'decimal:2',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    /**
     * Get the payment method.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the currency.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Scope to filter active fees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter effective fees for a given date.
     */
    public function scopeEffective($query, $date = null)
    {
        $date = $date ?? now()->toDateString();
        
        return $query->where(function ($q) use ($date) {
            $q->whereNull('effective_from')
              ->orWhere('effective_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('effective_until')
              ->orWhere('effective_until', '>=', $date);
        });
    }

    /**
     * Scope to filter by fee application type.
     */
    public function scopeAppliesTo($query, $type)
    {
        return $query->where(function ($q) use ($type) {
            $q->where('applies_to', $type)
              ->orWhere('applies_to', 'all');
        });
    }

    /**
     * Scope to filter by customer segment.
     */
    public function scopeForCustomerSegment($query, $segment)
    {
        return $query->where(function ($q) use ($segment) {
            $q->where('customer_segment', $segment)
              ->orWhere('customer_segment', 'all');
        });
    }

    /**
     * Scope to filter by region.
     */
    public function scopeForRegion($query, $region)
    {
        return $query->where(function ($q) use ($region) {
            $q->whereNull('region')
              ->orWhere('region', $region);
        });
    }

    /**
     * Calculate fee for a given amount.
     */
    public function calculateFee($amount, $customerSegment = 'all', $region = null)
    {
        if (!$this->isEffective() || !$this->appliesTo($customerSegment, $region)) {
            return 0;
        }

        switch ($this->fee_type) {
            case 'percentage':
                return $this->calculatePercentageFee($amount);
            case 'fixed':
                return $this->fixed_fee ?? $this->fee_value;
            case 'tiered':
                return $this->calculateTieredFee($amount);
            case 'mixed':
                return $this->calculateMixedFee($amount);
            default:
                return 0;
        }
    }

    /**
     * Calculate percentage-based fee.
     */
    protected function calculatePercentageFee($amount)
    {
        $fee = ($amount * $this->fee_value) / 100;
        
        if ($this->min_fee && $fee < $this->min_fee) {
            $fee = $this->min_fee;
        }
        
        if ($this->max_fee && $fee > $this->max_fee) {
            $fee = $this->max_fee;
        }
        
        return $fee;
    }

    /**
     * Calculate tiered fee based on amount ranges.
     */
    protected function calculateTieredFee($amount)
    {
        if (!$this->fee_tiers) {
            return 0;
        }

        foreach ($this->fee_tiers as $tier) {
            $minAmount = $tier['min_amount'] ?? 0;
            $maxAmount = $tier['max_amount'] ?? PHP_FLOAT_MAX;
            
            if ($amount >= $minAmount && $amount <= $maxAmount) {
                if ($tier['type'] === 'percentage') {
                    return ($amount * $tier['value']) / 100;
                } else {
                    return $tier['value'];
                }
            }
        }

        return 0;
    }

    /**
     * Calculate mixed fee (percentage + fixed).
     */
    protected function calculateMixedFee($amount)
    {
        $percentageFee = ($amount * $this->fee_value) / 100;
        $totalFee = $percentageFee + $this->fixed_fee;
        
        if ($this->min_fee && $totalFee < $this->min_fee) {
            $totalFee = $this->min_fee;
        }
        
        if ($this->max_fee && $totalFee > $this->max_fee) {
            $totalFee = $this->max_fee;
        }
        
        return $totalFee;
    }

    /**
     * Check if the fee is currently effective.
     */
    public function isEffective($date = null)
    {
        $date = $date ?? now()->toDateString();
        
        $effectiveFrom = $this->effective_from ? $this->effective_from->toDateString() : null;
        $effectiveUntil = $this->effective_until ? $this->effective_until->toDateString() : null;
        
        if ($effectiveFrom && $date < $effectiveFrom) {
            return false;
        }
        
        if ($effectiveUntil && $date > $effectiveUntil) {
            return false;
        }
        
        return $this->status === 'active';
    }

    /**
     * Check if the fee applies to a customer segment and region.
     */
    public function appliesTo($customerSegment = 'all', $region = null)
    {
        $segmentMatch = $this->customer_segment === 'all' || $this->customer_segment === $customerSegment;
        $regionMatch = !$this->region || $this->region === $region;
        
        return $segmentMatch && $regionMatch;
    }
}
