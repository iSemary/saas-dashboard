<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Crypt;

class CustomerPaymentMethod extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "customer payment method";
    public $pluralTitle = "customer payment methods";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'payment_method_id',
        'gateway_token',
        'gateway_customer_id',
        'method_details',
        'payment_type',
        'last_four',
        'brand',
        'expiry_month',
        'expiry_year',
        'holder_name',
        'billing_address',
        'is_default',
        'is_verified',
        'status',
        'verified_at',
        'last_used_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'billing_address' => 'array',
        'is_default' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'last_used_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the payment method.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get transactions made with this payment method.
     */
    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'customer_id', 'customer_id')
                    ->where('payment_method_id', $this->payment_method_id);
    }

    /**
     * Scope to filter active payment methods.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by customer.
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope to filter default payment methods.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to filter verified payment methods.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to filter by payment type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }

    /**
     * Get the decrypted method details.
     */
    public function getDecryptedDetailsAttribute()
    {
        if (!$this->method_details) {
            return null;
        }

        try {
            return json_decode(Crypt::decryptString($this->method_details), true);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set the method details (encrypt before storing).
     */
    public function setMethodDetailsAttribute($value)
    {
        if ($value) {
            $this->attributes['method_details'] = Crypt::encryptString(json_encode($value));
        } else {
            $this->attributes['method_details'] = null;
        }
    }

    /**
     * Get display name for the payment method.
     */
    public function getDisplayNameAttribute()
    {
        $parts = [];

        if ($this->brand) {
            $parts[] = ucfirst($this->brand);
        }

        if ($this->payment_type) {
            $parts[] = ucfirst($this->payment_type);
        }

        if ($this->last_four) {
            $parts[] = "****{$this->last_four}";
        }

        return implode(' ', $parts) ?: 'Payment Method';
    }

    /**
     * Check if payment method is expired (for cards).
     */
    public function isExpired()
    {
        if (!$this->expiry_month || !$this->expiry_year) {
            return false;
        }

        $expiryDate = \Carbon\Carbon::createFromDate($this->expiry_year, $this->expiry_month, 1)->endOfMonth();
        return $expiryDate->isPast();
    }

    /**
     * Check if payment method is expiring soon (within 30 days).
     */
    public function isExpiringSoon($days = 30)
    {
        if (!$this->expiry_month || !$this->expiry_year) {
            return false;
        }

        $expiryDate = \Carbon\Carbon::createFromDate($this->expiry_year, $this->expiry_month, 1)->endOfMonth();
        return $expiryDate->isBefore(now()->addDays($days));
    }

    /**
     * Set as default payment method for the customer.
     */
    public function setAsDefault()
    {
        // Remove default flag from other payment methods for this customer
        static::where('customer_id', $this->customer_id)
              ->where('id', '!=', $this->id)
              ->update(['is_default' => false]);

        // Set this as default
        $this->is_default = true;
        $this->save();

        return $this;
    }

    /**
     * Mark payment method as verified.
     */
    public function markAsVerified()
    {
        $this->is_verified = true;
        $this->verified_at = now();
        $this->save();

        return $this;
    }

    /**
     * Update last used timestamp.
     */
    public function updateLastUsed()
    {
        $this->last_used_at = now();
        $this->save();

        return $this;
    }

    /**
     * Get formatted expiry date.
     */
    public function getFormattedExpiryAttribute()
    {
        if (!$this->expiry_month || !$this->expiry_year) {
            return null;
        }

        return sprintf('%02d/%s', $this->expiry_month, substr($this->expiry_year, -2));
    }

    /**
     * Get billing address as formatted string.
     */
    public function getFormattedBillingAddressAttribute()
    {
        if (!$this->billing_address) {
            return null;
        }

        $address = $this->billing_address;
        $parts = [];

        if (isset($address['line1'])) {
            $parts[] = $address['line1'];
        }

        if (isset($address['line2'])) {
            $parts[] = $address['line2'];
        }

        if (isset($address['city'])) {
            $parts[] = $address['city'];
        }

        if (isset($address['state'])) {
            $parts[] = $address['state'];
        }

        if (isset($address['postal_code'])) {
            $parts[] = $address['postal_code'];
        }

        if (isset($address['country'])) {
            $parts[] = $address['country'];
        }

        return implode(', ', array_filter($parts));
    }

    /**
     * Check if payment method can be used for transactions.
     */
    public function canBeUsed()
    {
        return $this->status === 'active' && 
               !$this->isExpired() && 
               $this->paymentMethod->status === 'active';
    }

    /**
     * Get usage statistics for this payment method.
     */
    public function getUsageStats($days = 30)
    {
        $transactions = $this->transactions()
                             ->where('created_at', '>=', now()->subDays($days))
                             ->get();

        return [
            'total_transactions' => $transactions->count(),
            'successful_transactions' => $transactions->where('status', 'completed')->count(),
            'failed_transactions' => $transactions->where('status', 'failed')->count(),
            'total_amount' => $transactions->where('status', 'completed')->sum('amount'),
            'success_rate' => $transactions->count() > 0 
                ? round(($transactions->where('status', 'completed')->count() / $transactions->count()) * 100, 2)
                : 0,
        ];
    }
}
