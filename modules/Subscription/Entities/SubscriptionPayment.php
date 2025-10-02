<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Utilities\Entities\Currency;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $connection = "landlord";

    protected $fillable = [
        'subscription_id', 'invoice_id', 'payment_transaction_id', 'payment_id',
        'external_payment_id', 'amount', 'currency_id', 'payment_type',
        'payment_method_type', 'payment_method_details', 'attempted_at',
        'processed_at', 'failed_at', 'refunded_at', 'failure_reason',
        'retry_count', 'next_retry_at', 'gateway_response', 'metadata', 'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2', 'attempted_at' => 'datetime', 'processed_at' => 'datetime',
        'failed_at' => 'datetime', 'refunded_at' => 'datetime', 'retry_count' => 'integer',
        'next_retry_at' => 'datetime', 'gateway_response' => 'array', 'metadata' => 'array',
    ];

    public function subscription() { return $this->belongsTo(PlanSubscription::class, 'subscription_id'); }
    public function invoice() { return $this->belongsTo(SubscriptionInvoice::class, 'invoice_id'); }
    public function currency() { return $this->belongsTo(Currency::class); }
    public function paymentTransaction() { return $this->belongsTo(\Modules\Payment\Entities\PaymentTransaction::class, 'payment_transaction_id'); }
}
