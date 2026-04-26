<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customer\Entities\Brand;
use Modules\Payment\Entities\CustomerPaymentMethod;
use OwenIt\Auditing\Contracts\Auditable;

class BrandBillingProfile extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    protected $fillable = [
        'brand_id',
        'default_gateway',
        'stripe_customer_id',
        'paypal_payer_id',
        'default_payment_method_id',
        'tax_id',
        'tax_id_type',
        'billing_address',
        'billing_email',
        'billing_phone',
        'account_balance',
        'currency_code',
        'auto_pay',
        'paperless_billing',
        'invoice_email_cc',
        'status',
    ];

    protected $casts = [
        'account_balance' => 'decimal:2',
        'auto_pay' => 'boolean',
        'paperless_billing' => 'boolean',
        'billing_address' => 'array',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function defaultPaymentMethod()
    {
        return $this->belongsTo(CustomerPaymentMethod::class, 'default_payment_method_id');
    }

    public function paymentMethods()
    {
        return $this->hasMany(CustomerPaymentMethod::class, 'brand_id', 'brand_id');
    }

    /**
     * Check if the profile has an active payment method.
     */
    public function hasPaymentMethod(): bool
    {
        return $this->default_payment_method_id !== null;
    }

    /**
     * Add credit to the account balance.
     */
    public function addCredit(float $amount): void
    {
        $this->increment('account_balance', $amount);
    }

    /**
     * Deduct from the account balance.
     */
    public function deductBalance(float $amount): void
    {
        $this->decrement('account_balance', $amount);
    }

    /**
     * Get the gateway-specific customer ID.
     */
    public function getGatewayCustomerId(string $gateway): ?string
    {
        return match($gateway) {
            'stripe' => $this->stripe_customer_id,
            'paypal' => $this->paypal_payer_id,
            default => null,
        };
    }

    /**
     * Set the gateway-specific customer ID.
     */
    public function setGatewayCustomerId(string $gateway, string $customerId): void
    {
        match($gateway) {
            'stripe' => $this->update(['stripe_customer_id' => $customerId]),
            'paypal' => $this->update(['paypal_payer_id' => $customerId]),
            default => null,
        };
    }
}
