<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Modules\Utilities\Entities\Currency;

class PaymentMethodLimit extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "payment method limit";
    public $pluralTitle = "payment method limits";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_method_id',
        'currency_id',
        'limit_type',
        'min_limit',
        'max_limit',
        'limit_duration',
        'transaction_count_limit',
        'customer_segment',
        'region',
        'status',
        'conditions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_limit' => 'decimal:2',
        'max_limit' => 'decimal:2',
        'limit_duration' => 'integer',
        'transaction_count_limit' => 'integer',
        'conditions' => 'array',
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
     * Scope to filter active limits.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by limit type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('limit_type', $type);
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
     * Check if an amount is within the allowed limits.
     */
    public function isAmountAllowed($amount, $customerSegment = 'all', $region = null)
    {
        if (!$this->appliesTo($customerSegment, $region)) {
            return true;
        }

        if ($this->min_limit && $amount < $this->min_limit) {
            return false;
        }

        if ($this->max_limit && $amount > $this->max_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if the limit applies to a customer segment and region.
     */
    public function appliesTo($customerSegment = 'all', $region = null)
    {
        $segmentMatch = $this->customer_segment === 'all' || $this->customer_segment === $customerSegment;
        $regionMatch = !$this->region || $this->region === $region;
        
        return $segmentMatch && $regionMatch && $this->status === 'active';
    }

    /**
     * Get the time window for rolling limits.
     */
    public function getTimeWindow()
    {
        if (!$this->limit_duration) {
            return null;
        }

        switch ($this->limit_type) {
            case 'daily':
                return now()->subDay();
            case 'weekly':
                return now()->subWeek();
            case 'monthly':
                return now()->subMonth();
            case 'yearly':
                return now()->subYear();
            default:
                return now()->subHours($this->limit_duration);
        }
    }

    /**
     * Check if transaction count limit is exceeded.
     */
    public function isTransactionCountExceeded($customerId, $paymentMethodId)
    {
        if (!$this->transaction_count_limit) {
            return false;
        }

        $timeWindow = $this->getTimeWindow();
        if (!$timeWindow) {
            return false;
        }

        $transactionCount = PaymentTransaction::where('customer_id', $customerId)
            ->where('payment_method_id', $paymentMethodId)
            ->where('created_at', '>=', $timeWindow)
            ->where('status', '!=', 'failed')
            ->count();

        return $transactionCount >= $this->transaction_count_limit;
    }

    /**
     * Check if amount limit is exceeded for rolling window.
     */
    public function isAmountLimitExceeded($customerId, $paymentMethodId, $newAmount)
    {
        if (!$this->max_limit || $this->limit_type === 'transaction') {
            return false;
        }

        $timeWindow = $this->getTimeWindow();
        if (!$timeWindow) {
            return false;
        }

        $totalAmount = PaymentTransaction::where('customer_id', $customerId)
            ->where('payment_method_id', $paymentMethodId)
            ->where('currency_id', $this->currency_id)
            ->where('created_at', '>=', $timeWindow)
            ->where('status', '!=', 'failed')
            ->sum('amount');

        return ($totalAmount + $newAmount) > $this->max_limit;
    }

    /**
     * Get validation error message for limit violation.
     */
    public function getViolationMessage($violationType, $amount = null)
    {
        $currency = $this->currency->code ?? '';
        
        switch ($violationType) {
            case 'min_amount':
                return "Minimum transaction amount is {$currency} {$this->min_limit}";
            case 'max_amount':
                return "Maximum transaction amount is {$currency} {$this->max_limit}";
            case 'transaction_count':
                return "Maximum {$this->transaction_count_limit} transactions allowed per {$this->limit_type}";
            case 'amount_limit':
                return "Maximum {$currency} {$this->max_limit} allowed per {$this->limit_type}";
            default:
                return "Transaction limit exceeded";
        }
    }
}
