<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Utilities\Entities\Currency;
use Modules\Customer\Entities\Brand;
use App\Models\User;
use OwenIt\Auditing\Contracts\Auditable;

class PlanSubscription extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    protected $fillable = [
        'subscription_id',
        'brand_id',
        'user_id',
        'plan_id',
        'currency_id',
        'country_code',
        'price',
        'setup_fee',
        'billing_cycle',
        'billing_interval',
        'user_count',
        'trial_starts_at',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'next_billing_at',
        'canceled_at',
        'expires_at',
        'cancellation_reason',
        'cancellation_feedback',
        'applied_discounts',
        'subscription_data',
        'status',
        'auto_renew'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'billing_interval' => 'integer',
        'user_count' => 'integer',
        'trial_starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'next_billing_at' => 'datetime',
        'canceled_at' => 'datetime',
        'expires_at' => 'datetime',
        'applied_discounts' => 'array',
        'subscription_data' => 'array',
    ];

    /**
     * Get the brand that owns the subscription.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan for the subscription.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the currency for the subscription.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the subscription invoices.
     */
    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class, 'subscription_id');
    }

    /**
     * Get the subscription payments.
     */
    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class, 'subscription_id');
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for trial subscriptions.
     */
    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }

    /**
     * Scope for canceled subscriptions.
     */
    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    /**
     * Scope for expired subscriptions.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope for subscriptions ending soon.
     */
    public function scopeEndingSoon($query, $days = 7)
    {
        return $query->where('ends_at', '<=', now()->addDays($days))
                    ->where('ends_at', '>', now());
    }

    /**
     * Scope for subscriptions due for billing.
     */
    public function scopeDueForBilling($query)
    {
        return $query->where('next_billing_at', '<=', now())
                    ->where('status', 'active')
                    ->where('auto_renew', 'enabled');
    }

    /**
     * Check if subscription is currently active.
     */
    public function isActive()
    {
        return $this->status === 'active' && 
               (!$this->ends_at || $this->ends_at->isFuture());
    }

    /**
     * Check if subscription is in trial period.
     */
    public function isOnTrial()
    {
        return $this->status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is canceled.
     */
    public function isCanceled()
    {
        return $this->status === 'canceled' || $this->canceled_at;
    }

    /**
     * Check if subscription has expired.
     */
    public function isExpired()
    {
        return $this->status === 'expired' || 
               ($this->expires_at && $this->expires_at->isPast());
    }

    /**
     * Get days remaining in current period.
     */
    public function getDaysRemaining()
    {
        if ($this->isOnTrial()) {
            return $this->trial_ends_at->diffInDays(now());
        }

        if ($this->ends_at) {
            return max(0, $this->ends_at->diffInDays(now()));
        }

        return null;
    }

    /**
     * Get trial days remaining.
     */
    public function getTrialDaysRemaining()
    {
        if (!$this->isOnTrial()) {
            return 0;
        }

        return max(0, $this->trial_ends_at->diffInDays(now()));
    }

    /**
     * Cancel subscription.
     */
    public function cancel($reason = null, $feedback = null, $immediately = false)
    {
        $this->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'cancellation_reason' => $reason,
            'cancellation_feedback' => $feedback,
            'auto_renew' => 'disabled',
            'expires_at' => $immediately ? now() : $this->ends_at,
        ]);

        return $this;
    }

    /**
     * Resume canceled subscription.
     */
    public function resume()
    {
        if ($this->isCanceled() && !$this->isExpired()) {
            $this->update([
                'status' => 'active',
                'canceled_at' => null,
                'cancellation_reason' => null,
                'cancellation_feedback' => null,
                'auto_renew' => 'enabled',
                'expires_at' => null,
            ]);
        }

        return $this;
    }

    /**
     * Extend subscription by days.
     */
    public function extend($days)
    {
        $this->update([
            'ends_at' => $this->ends_at ? $this->ends_at->addDays($days) : now()->addDays($days),
            'next_billing_at' => $this->next_billing_at ? $this->next_billing_at->addDays($days) : null,
        ]);

        return $this;
    }

    /**
     * Renew subscription for next billing cycle.
     */
    public function renew()
    {
        $billingDays = $this->getBillingCycleDays();
        
        $this->update([
            'starts_at' => $this->ends_at ?: now(),
            'ends_at' => ($this->ends_at ?: now())->addDays($billingDays),
            'next_billing_at' => ($this->ends_at ?: now())->addDays($billingDays),
        ]);

        return $this;
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
            'lifetime' => 36500,
        ];

        return ($days[$this->billing_cycle] ?? 30) * $this->billing_interval;
    }

    /**
     * Calculate prorated amount for upgrade/downgrade.
     */
    public function calculateProratedAmount($newPrice)
    {
        if (!$this->ends_at) {
            return $newPrice;
        }

        $totalDays = $this->getBillingCycleDays();
        $remainingDays = $this->ends_at->diffInDays(now());
        $usedDays = $totalDays - $remainingDays;

        $usedAmount = ($this->price / $totalDays) * $usedDays;
        $newAmount = ($newPrice / $totalDays) * $remainingDays;

        return $newAmount - ($this->price - $usedAmount);
    }

    /**
     * Get formatted price with currency.
     */
    public function getFormattedPriceAttribute()
    {
        return $this->currency->symbol . number_format($this->price, 2);
    }

    /**
     * Get subscription status badge color.
     */
    public function getStatusColorAttribute()
    {
        return [
            'trial' => 'info',
            'active' => 'success',
            'past_due' => 'warning',
            'canceled' => 'secondary',
            'expired' => 'danger',
            'suspended' => 'dark',
        ][$this->status] ?? 'secondary';
    }
}
