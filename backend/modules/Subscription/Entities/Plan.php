<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Localization\Traits\Translatable;
use OwenIt\Auditing\Contracts\Auditable;

class Plan extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, Translatable;

    protected $connection = "landlord";

    public $singleTitle = "plan";
    public $pluralTitle = "plans";

    protected $fillable = [
        'name',
        'slug',
        'description',
        'features_summary',
        'price',
        'currency',
        'billing_period',
        'sort_order',
        'is_popular',
        'is_custom',
        'metadata',
        'status'
    ];

    protected $translatableColumns = ['name', 'description', 'features_summary'];

    protected $casts = [
        'metadata' => 'array',
        'is_popular' => 'boolean',
        'is_custom' => 'boolean',
        'sort_order' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get the plan prices.
     */
    public function prices()
    {
        return $this->hasMany(PlanPrice::class);
    }

    /**
     * Get the plan prices by users.
     */
    public function pricesByUsers()
    {
        return $this->hasMany(PlanPriceByUser::class);
    }

    /**
     * Get the plan features.
     */
    public function features()
    {
        return $this->hasMany(PlanFeature::class)->orderBy('sort_order');
    }

    /**
     * Get the plan discounts.
     */
    public function discounts()
    {
        return $this->hasMany(PlanDiscount::class);
    }

    /**
     * Get the plan subscriptions.
     */
    public function subscriptions()
    {
        return $this->hasMany(PlanSubscription::class);
    }

    /**
     * Get the plan price history.
     */
    public function priceHistory()
    {
        return $this->hasMany(PlanPriceHistory::class)->orderBy('change_date', 'desc');
    }

    /**
     * Get the plan billing cycles.
     */
    public function billingCycles()
    {
        return $this->hasMany(PlanBillingCycle::class)->orderBy('sort_order');
    }

    /**
     * Get the plan trials.
     */
    public function trials()
    {
        return $this->hasMany(PlanTrial::class);
    }

    /**
     * Get upgrade rules from this plan.
     */
    public function upgradeRulesFrom()
    {
        return $this->hasMany(PlanUpgradeRule::class, 'from_plan_id');
    }

    /**
     * Get upgrade rules to this plan.
     */
    public function upgradeRulesTo()
    {
        return $this->hasMany(PlanUpgradeRule::class, 'to_plan_id');
    }

    /**
     * Scope for active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for popular plans.
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope for custom plans.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_custom', true);
    }

    /**
     * Scope for ordered plans.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get price for specific currency and country.
     */
    public function getPriceFor($currencyCode, $countryCode = null, $billingCycle = 'monthly')
    {
        return $this->prices()
                   ->whereHas('currency', function ($query) use ($currencyCode) {
                       $query->where('code', $currencyCode);
                   })
                   ->where('country_code', $countryCode)
                   ->where('billing_cycle', $billingCycle)
                   ->where('status', 'active')
                   ->first();
    }

    /**
     * Get usage-based price for specific user count.
     */
    public function getUserBasedPrice($userCount, $currencyCode, $countryCode = null, $billingCycle = 'monthly')
    {
        return $this->pricesByUsers()
                   ->whereHas('currency', function ($query) use ($currencyCode) {
                       $query->where('code', $currencyCode);
                   })
                   ->where('country_code', $countryCode)
                   ->where('billing_cycle', $billingCycle)
                   ->where('min_users', '<=', $userCount)
                   ->where(function ($query) use ($userCount) {
                       $query->where('max_users', '>=', $userCount)
                             ->orWhereNull('max_users');
                   })
                   ->where('status', 'active')
                   ->first();
    }

    /**
     * Get trial configuration for country.
     */
    public function getTrialFor($countryCode = null)
    {
        return $this->trials()
                   ->where('country_code', $countryCode)
                   ->where('status', 'active')
                   ->first() ?: $this->trials()
                                   ->whereNull('country_code')
                                   ->where('status', 'active')
                                   ->first();
    }

    /**
     * Check if plan has feature.
     */
    public function hasFeature($featureKey)
    {
        return $this->features()
                   ->where('feature_key', $featureKey)
                   ->where('status', 'active')
                   ->exists();
    }

    /**
     * Get feature value.
     */
    public function getFeatureValue($featureKey, $default = null)
    {
        $feature = $this->features()
                       ->where('feature_key', $featureKey)
                       ->where('status', 'active')
                       ->first();

        if (!$feature) {
            return $default;
        }

        switch ($feature->feature_type) {
            case 'boolean':
                return (bool) $feature->feature_value;
            case 'numeric':
                return $feature->is_unlimited ? -1 : (int) $feature->numeric_limit;
            case 'json':
                return json_decode($feature->feature_value, true);
            default:
                return $feature->feature_value;
        }
    }

    /**
     * Get active discounts.
     */
    public function getActiveDiscounts()
    {
        return $this->discounts()
                   ->where('status', 'active')
                   ->where('start_date', '<=', now())
                   ->where(function ($query) {
                       $query->whereNull('end_date')
                             ->orWhere('end_date', '>=', now());
                   })
                   ->get();
    }

    /**
     * Check if plan can be upgraded to another plan.
     */
    public function canUpgradeTo(Plan $targetPlan)
    {
        return $this->upgradeRulesFrom()
                   ->where('to_plan_id', $targetPlan->id)
                   ->where('is_allowed', true)
                   ->where('status', 'active')
                   ->exists();
    }

    /**
     * Get allowed upgrade/downgrade plans.
     */
    public function getAllowedChanges($ruleType = null)
    {
        $query = $this->upgradeRulesFrom()
                     ->with('toPlan')
                     ->where('is_allowed', true)
                     ->where('status', 'active');

        if ($ruleType) {
            $query->where('rule_type', $ruleType);
        }

        return $query->get()->pluck('toPlan');
    }

    /**
     * Get active subscriptions count.
     */
    public function getActiveSubscriptionsCount()
    {
        return $this->subscriptions()
                   ->whereIn('status', ['trial', 'active'])
                   ->count();
    }

    /**
     * Get monthly recurring revenue for this plan.
     */
    public function getMonthlyRecurringRevenue($currencyCode = 'USD')
    {
        return $this->subscriptions()
                   ->where('status', 'active')
                   ->whereHas('currency', function ($query) use ($currencyCode) {
                       $query->where('code', $currencyCode);
                   })
                   ->sum('price');
    }
}
