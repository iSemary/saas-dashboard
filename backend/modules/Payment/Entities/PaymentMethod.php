<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Modules\Utilities\Entities\Currency;

class PaymentMethod extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "payment method";
    public $pluralTitle = "payment methods";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'processor_type',
        'gateway_name',
        'country_codes',
        'supported_currencies',
        'is_global',
        'region_restrictions',
        'status',
        'authentication_type',
        'priority',
        'success_rate',
        'average_processing_time',
        'features',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'country_codes' => 'array',
        'supported_currencies' => 'array',
        'is_global' => 'boolean',
        'region_restrictions' => 'array',
        'priority' => 'integer',
        'success_rate' => 'decimal:2',
        'average_processing_time' => 'integer',
        'features' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the payment method currencies.
     */
    public function paymentMethodCurrencies()
    {
        return $this->hasMany(PaymentMethodCurrency::class);
    }

    /**
     * Get the supported currencies through the pivot table.
     */
    public function currencies()
    {
        return $this->belongsToMany(Currency::class, 'payment_method_currencies')
                    ->withPivot(['processing_currency_id', 'settlement_days', 'settlement_schedule', 'conversion_rate', 'auto_conversion', 'status'])
                    ->withTimestamps();
    }

    /**
     * Get the payment method fees.
     */
    public function fees()
    {
        return $this->hasMany(PaymentMethodFee::class);
    }

    /**
     * Get the payment method limits.
     */
    public function limits()
    {
        return $this->hasMany(PaymentMethodLimit::class);
    }

    /**
     * Get the payment method configurations.
     */
    public function configurations()
    {
        return $this->hasMany(PaymentMethodConfiguration::class);
    }

    /**
     * Get the payment transactions.
     */
    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Get the gateway logs.
     */
    public function gatewayLogs()
    {
        return $this->hasMany(PaymentGatewayLog::class);
    }

    /**
     * Get the customer payment methods.
     */
    public function customerPaymentMethods()
    {
        return $this->hasMany(CustomerPaymentMethod::class);
    }

    /**
     * Get the routing rules where this is the target method.
     */
    public function targetRoutingRules()
    {
        return $this->hasMany(PaymentRoutingRule::class, 'target_payment_method_id');
    }

    /**
     * Get the routing rules where this is the fallback method.
     */
    public function fallbackRoutingRules()
    {
        return $this->hasMany(PaymentRoutingRule::class, 'fallback_payment_method_id');
    }

    /**
     * Scope to filter active payment methods.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter global payment methods.
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    /**
     * Scope to filter by processor type.
     */
    public function scopeByProcessor($query, $processor)
    {
        return $query->where('processor_type', $processor);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrderedByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Check if payment method supports a specific currency.
     */
    public function supportsCurrency($currencyCode)
    {
        return in_array($currencyCode, $this->supported_currencies ?? []);
    }

    /**
     * Check if payment method is available in a specific country.
     */
    public function availableInCountry($countryCode)
    {
        if ($this->is_global) {
            return true;
        }

        return in_array($countryCode, $this->country_codes ?? []);
    }

    /**
     * Get configuration value for a specific environment.
     */
    public function getConfigValue($key, $environment = 'production')
    {
        $config = $this->configurations()
                       ->where('config_key', $key)
                       ->where('environment', $environment)
                       ->where('status', 'active')
                       ->first();

        return $config ? $config->config_value : null;
    }

    /**
     * Calculate success rate based on recent transactions.
     */
    public function calculateSuccessRate($days = 30)
    {
        $totalTransactions = $this->transactions()
                                  ->where('created_at', '>=', now()->subDays($days))
                                  ->count();

        if ($totalTransactions === 0) {
            return 0;
        }

        $successfulTransactions = $this->transactions()
                                       ->where('created_at', '>=', now()->subDays($days))
                                       ->where('status', 'completed')
                                       ->count();

        return round(($successfulTransactions / $totalTransactions) * 100, 2);
    }
}
