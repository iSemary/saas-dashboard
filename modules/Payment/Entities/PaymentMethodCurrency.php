<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Modules\Utilities\Entities\Currency;

class PaymentMethodCurrency extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "payment method currency";
    public $pluralTitle = "payment method currencies";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_method_id',
        'currency_id',
        'processing_currency_id',
        'settlement_days',
        'settlement_schedule',
        'conversion_rate',
        'auto_conversion',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settlement_days' => 'integer',
        'conversion_rate' => 'decimal:6',
        'auto_conversion' => 'boolean',
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
     * Get the processing currency.
     */
    public function processingCurrency()
    {
        return $this->belongsTo(Currency::class, 'processing_currency_id');
    }

    /**
     * Scope to filter active currency configurations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by settlement schedule.
     */
    public function scopeBySettlementSchedule($query, $schedule)
    {
        return $query->where('settlement_schedule', $schedule);
    }

    /**
     * Get the effective conversion rate.
     */
    public function getEffectiveConversionRate()
    {
        if ($this->conversion_rate) {
            return $this->conversion_rate;
        }

        // Fall back to the currency's exchange rate
        return $this->currency->exchange_rate ?? 1;
    }

    /**
     * Calculate settlement date based on settlement days and schedule.
     */
    public function calculateSettlementDate($transactionDate = null)
    {
        $baseDate = $transactionDate ? carbon($transactionDate) : now();

        switch ($this->settlement_schedule) {
            case 'instant':
                return $baseDate;
            case 'daily':
                return $baseDate->addDays($this->settlement_days);
            case 'weekly':
                return $baseDate->addWeeks(ceil($this->settlement_days / 7));
            case 'monthly':
                return $baseDate->addMonths(ceil($this->settlement_days / 30));
            default:
                return $baseDate->addDays($this->settlement_days);
        }
    }
}
