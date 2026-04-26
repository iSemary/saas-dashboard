<?php

namespace Modules\Subscription\Services;

use Modules\Customer\Entities\Brand;
use Modules\Customer\Entities\BrandModuleSubscription;
use Modules\Subscription\Entities\Plan;
use Modules\Subscription\Entities\PlanSubscription;
use Modules\Subscription\Entities\SubscriptionInvoice;
use Modules\Subscription\Entities\BrandBillingProfile;
use Modules\Utilities\Entities\Currency;
use Illuminate\Database\Eloquent\Collection;

class TenantBillingService
{
    public function __construct(
        private InvoiceGenerationService $invoiceService,
        private ProrationCalculator $prorationCalculator,
    ) {}

    /**
     * Get comprehensive billing overview for a brand.
     */
    public function getBillingOverview(int $brandId): array
    {
        $brand = Brand::find($brandId);
        if (!$brand) {
            throw new \InvalidArgumentException('Brand not found');
        }

        $subscription = PlanSubscription::where('brand_id', $brandId)
            ->whereIn('status', ['active', 'trial'])
            ->with(['plan', 'currency'])
            ->first();

        $billingProfile = BrandBillingProfile::where('brand_id', $brandId)->first();
        $activeModules = $this->getActiveModules($brandId);
        
        // Calculate upcoming invoice estimate
        $upcomingInvoice = $subscription ? $this->invoiceService->previewUpcomingInvoice($subscription) : null;

        return [
            'brand_id' => $brandId,
            'brand_name' => $brand->name,
            'subscription' => $subscription ? $this->formatSubscription($subscription) : null,
            'active_modules' => $activeModules->map(fn($m) => $this->formatModule($m)),
            'module_count' => $activeModules->count(),
            'billing_profile' => $billingProfile ? $this->formatBillingProfile($billingProfile) : null,
            'upcoming_invoice' => $upcomingInvoice,
            'payment_status' => $this->getPaymentStatus($brandId),
            'balance' => $billingProfile?->account_balance ?? 0,
            'currency' => $subscription?->currency?->code ?? 'USD',
        ];
    }

    /**
     * Get available plans for a brand (with pricing for their currency).
     */
    public function getAvailablePlans(int $brandId, ?string $currencyCode = null): Collection
    {
        $currencyCode = $currencyCode ?? $this->getBrandCurrency($brandId);
        
        return Plan::where('status', 'active')
            ->with(['prices' => function ($query) use ($currencyCode) {
                $query->whereHas('currency', fn($q) => $q->where('code', $currencyCode))
                    ->where('status', 'active');
            }, 'features'])
            ->ordered()
            ->get();
    }

    /**
     * Get available add-on modules for a brand.
     */
    public function getAvailableModules(int $brandId): Collection
    {
        $brandModuleIds = BrandModuleSubscription::where('brand_id', $brandId)
            ->pluck('module_key')
            ->toArray();

        return \Modules\Utilities\Entities\Module::where('is_addon', true)
            ->where('status', 'active')
            ->with('currency')
            ->get()
            ->map(function ($module) use ($brandModuleIds) {
                $subscription = BrandModuleSubscription::where('brand_id', $brandId)
                    ->where('module_key', $module->module_key)
                    ->first();
                
                return [
                    'id' => $module->id,
                    'module_key' => $module->module_key,
                    'name' => $module->name,
                    'description' => $module->description,
                    'base_price' => $module->base_price,
                    'currency_code' => $module->currency?->code,
                    'billing_cycle' => $module->billing_cycle,
                    'trial_days' => $module->trial_days,
                    'is_subscribed' => in_array($module->module_key, $brandModuleIds),
                    'subscription' => $subscription ? [
                        'id' => $subscription->id,
                        'status' => $subscription->subscription_status,
                        'price' => $subscription->price,
                        'current_period_end' => $subscription->current_period_end,
                    ] : null,
                ];
            });
    }

    /**
     * Get invoice history for a brand.
     */
    public function getInvoiceHistory(int $brandId, array $filters = [], int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = SubscriptionInvoice::where('brand_id', $brandId)
            ->with(['currency', 'items', 'payments']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('invoice_date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('invoice_date', '<=', $filters['to_date']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get payment history for a brand.
     */
    public function getPaymentHistory(int $brandId, array $filters = [], int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = \Modules\Subscription\Entities\SubscriptionPayment::where('brand_id', $brandId)
            ->with(['currency', 'invoice']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['gateway'])) {
            $query->where('gateway', $filters['gateway']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Format subscription for response.
     */
    private function formatSubscription(PlanSubscription $subscription): array
    {
        return [
            'id' => $subscription->id,
            'plan' => [
                'id' => $subscription->plan->id,
                'name' => $subscription->plan->name,
                'slug' => $subscription->plan->slug,
            ],
            'status' => $subscription->status,
            'price' => $subscription->price,
            'currency' => $subscription->currency?->code,
            'billing_cycle' => $subscription->billing_cycle,
            'trial_ends_at' => $subscription->trial_ends_at?->toDateTimeString(),
            'current_period_start' => $subscription->starts_at?->toDateTimeString(),
            'current_period_end' => $subscription->ends_at?->toDateTimeString(),
            'next_billing_at' => $subscription->next_billing_at?->toDateTimeString(),
            'cancel_at_period_end' => $subscription->canceled_at !== null && $subscription->ends_at > now(),
        ];
    }

    /**
     * Format module for response.
     */
    private function formatModule(BrandModuleSubscription $module): array
    {
        return [
            'id' => $module->id,
            'module_key' => $module->module_key,
            'status' => $module->subscription_status,
            'price' => $module->price,
            'billing_cycle' => $module->billing_cycle,
            'current_period_end' => $module->current_period_end?->toDateTimeString(),
            'next_billing_at' => $module->next_billing_at?->toDateTimeString(),
        ];
    }

    /**
     * Format billing profile for response.
     */
    private function formatBillingProfile(BrandBillingProfile $profile): array
    {
        return [
            'id' => $profile->id,
            'default_gateway' => $profile->default_gateway,
            'has_payment_method' => $profile->default_payment_method_id !== null,
            'billing_email' => $profile->billing_email,
            'tax_id' => $profile->tax_id,
            'account_balance' => $profile->account_balance,
            'auto_pay' => $profile->auto_pay,
        ];
    }

    /**
     * Get active modules for a brand.
     */
    private function getActiveModules(int $brandId): Collection
    {
        return BrandModuleSubscription::where('brand_id', $brandId)
            ->where('subscription_status', 'active')
            ->whereNotNull('price')
            ->get();
    }

    /**
     * Get payment status for a brand.
     */
    private function getPaymentStatus(int $brandId): string
    {
        $hasOverdue = SubscriptionInvoice::where('brand_id', $brandId)
            ->where('status', 'overdue')
            ->exists();

        if ($hasOverdue) {
            return 'past_due';
        }

        $subscription = PlanSubscription::where('brand_id', $brandId)
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if (!$subscription) {
            return 'no_subscription';
        }

        return $subscription->status;
    }

    /**
     * Get brand's default currency.
     */
    private function getBrandCurrency(int $brandId): string
    {
        $profile = BrandBillingProfile::where('brand_id', $brandId)->first();
        
        if ($profile && $profile->currency_code) {
            return $profile->currency_code;
        }

        $subscription = PlanSubscription::where('brand_id', $brandId)
            ->with('currency')
            ->first();

        return $subscription?->currency?->code ?? 'USD';
    }
}
