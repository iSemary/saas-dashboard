<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Modules\Utilities\Entities\Currency;

class PaymentTransaction extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "payment transaction";
    public $pluralTitle = "payment transactions";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'payment_method_id',
        'currency_id',
        'amount',
        'base_currency_amount',
        'exchange_rate_used',
        'customer_id',
        'merchant_account_id',
        'gateway_transaction_id',
        'gateway_reference',
        'transaction_type',
        'status',
        'gateway_response',
        'error_details',
        'metadata',
        'settlement_status',
        'settlement_date',
        'fees_breakdown',
        'total_fees',
        'net_amount',
        'invoice_number',
        'order_id',
        'description',
        'customer_ip',
        'user_agent',
        'billing_address',
        'shipping_address',
        'payment_method_details',
        'is_test',
        'processed_at',
        'settled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'base_currency_amount' => 'decimal:2',
        'exchange_rate_used' => 'decimal:6',
        'gateway_response' => 'array',
        'error_details' => 'array',
        'metadata' => 'array',
        'settlement_date' => 'datetime',
        'fees_breakdown' => 'array',
        'total_fees' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'is_test' => 'boolean',
        'processed_at' => 'datetime',
        'settled_at' => 'datetime',
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
     * Get the refunds for this transaction.
     */
    public function refunds()
    {
        return $this->hasMany(Refund::class, 'original_transaction_id');
    }

    /**
     * Get the chargebacks for this transaction.
     */
    public function chargebacks()
    {
        return $this->hasMany(Chargeback::class, 'transaction_id');
    }

    /**
     * Get the gateway logs for this transaction.
     */
    public function gatewayLogs()
    {
        return $this->hasMany(PaymentGatewayLog::class, 'transaction_id');
    }

    /**
     * Get the original transaction if this is a refund.
     */
    public function originalTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'original_transaction_id');
    }

    /**
     * Get refund transactions for this transaction.
     */
    public function refundTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'original_transaction_id')
                    ->where('transaction_type', 'refund');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter successful transactions.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to filter failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to filter by transaction type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope to filter by customer.
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope to filter test transactions.
     */
    public function scopeTestTransactions($query)
    {
        return $query->where('is_test', true);
    }

    /**
     * Scope to filter live transactions.
     */
    public function scopeLiveTransactions($query)
    {
        return $query->where('is_test', false);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter settled transactions.
     */
    public function scopeSettled($query)
    {
        return $query->where('settlement_status', 'settled');
    }

    /**
     * Check if transaction is successful.
     */
    public function isSuccessful()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is failed.
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if transaction can be refunded.
     */
    public function canBeRefunded()
    {
        if (!$this->isSuccessful()) {
            return false;
        }

        if ($this->transaction_type !== 'sale') {
            return false;
        }

        $totalRefunded = $this->refunds()->where('status', 'completed')->sum('amount');
        return $totalRefunded < $this->amount;
    }

    /**
     * Get the remaining refundable amount.
     */
    public function getRefundableAmount()
    {
        if (!$this->canBeRefunded()) {
            return 0;
        }

        $totalRefunded = $this->refunds()->where('status', 'completed')->sum('amount');
        return $this->amount - $totalRefunded;
    }

    /**
     * Check if transaction is fully refunded.
     */
    public function isFullyRefunded()
    {
        $totalRefunded = $this->refunds()->where('status', 'completed')->sum('amount');
        return $totalRefunded >= $this->amount;
    }

    /**
     * Check if transaction is partially refunded.
     */
    public function isPartiallyRefunded()
    {
        $totalRefunded = $this->refunds()->where('status', 'completed')->sum('amount');
        return $totalRefunded > 0 && $totalRefunded < $this->amount;
    }

    /**
     * Get formatted amount with currency symbol.
     */
    public function getFormattedAmountAttribute()
    {
        $currency = $this->currency;
        if (!$currency) {
            return number_format($this->amount, 2);
        }

        $formatted = number_format($this->amount, $currency->decimal_places);
        
        if ($currency->symbol_position === 'left') {
            return $currency->symbol . ' ' . $formatted;
        } else {
            return $formatted . ' ' . $currency->symbol;
        }
    }

    /**
     * Calculate net amount after fees.
     */
    public function calculateNetAmount()
    {
        return $this->amount - $this->total_fees;
    }

    /**
     * Update settlement status.
     */
    public function updateSettlementStatus($status, $date = null)
    {
        $this->settlement_status = $status;
        
        if ($status === 'settled' && !$this->settled_at) {
            $this->settled_at = $date ?? now();
        }
        
        $this->save();
    }

    /**
     * Add gateway log entry.
     */
    public function addGatewayLog($level, $operation, $requestData = null, $responseData = null, $errorMessage = null)
    {
        return $this->gatewayLogs()->create([
            'payment_method_id' => $this->payment_method_id,
            'log_level' => $level,
            'operation' => $operation,
            'request_data' => $requestData,
            'response_data' => $responseData,
            'error_message' => $errorMessage,
        ]);
    }
}
