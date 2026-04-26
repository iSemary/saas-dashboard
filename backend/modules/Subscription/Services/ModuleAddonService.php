<?php

namespace Modules\Subscription\Services;

use Carbon\Carbon;
use Modules\Customer\Entities\BrandModuleSubscription;
use Modules\Subscription\DTOs\AddModuleData;
use Modules\Subscription\DTOs\RemoveModuleData;
use Modules\Subscription\DTOs\ProrationResult;
use Modules\Subscription\Entities\PlanSubscription;
use Modules\Utilities\Entities\Module;

class ModuleAddonService
{
    public function __construct(
        private ProrationCalculator $prorationCalculator,
        private InvoiceGenerationService $invoiceService,
    ) {}

    /**
     * Subscribe to an add-on module.
     */
    public function subscribeToModule(int $brandId, AddModuleData $data): array
    {
        $module = Module::findOrFail($data->module_id);
        
        if (!$module->is_addon) {
            throw new \InvalidArgumentException('This module is not available as an add-on');
        }

        if (!$module->base_price) {
            throw new \InvalidArgumentException('Module does not have a price configured');
        }

        // Check if already subscribed
        $existing = BrandModuleSubscription::where('brand_id', $brandId)
            ->where('module_key', $module->module_key)
            ->first();

        if ($existing && $existing->subscription_status === 'active') {
            throw new \InvalidArgumentException('Already subscribed to this module');
        }

        // Get parent subscription for billing alignment
        $parentSubscription = PlanSubscription::where('brand_id', $brandId)
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if (!$parentSubscription) {
            throw new \InvalidArgumentException('No active subscription found. Please subscribe to a plan first.');
        }

        // Calculate pricing
        $price = $this->calculateModulePrice($module, $data->billing_cycle);
        $currencyId = $module->currency_id ?? $parentSubscription->currency_id;

        // Calculate period dates (aligned with parent subscription)
        $periodDates = $this->calculatePeriodDates($data->billing_cycle, $parentSubscription);

        // Calculate proration if adding mid-cycle
        $proration = null;
        if ($data->immediate && $parentSubscription->current_period_start) {
            $proration = $this->calculateProration(
                0, // No current charge
                $price,
                $parentSubscription->current_period_start,
                $parentSubscription->current_period_end ?? $periodDates['end'],
            );
        }

        // Create or update subscription
        if ($existing) {
            $existing->update([
                'subscription_status' => 'active',
                'module_id' => $module->id,
                'price' => $price,
                'currency_id' => $currencyId,
                'billing_cycle' => $data->billing_cycle,
                'subscription_start' => $periodDates['start'],
                'current_period_start' => $periodDates['start'],
                'current_period_end' => $periodDates['end'],
                'next_billing_at' => $periodDates['end'],
                'subscribed_at' => Carbon::now(),
                'canceled_at' => null,
                'cancel_at_period_end' => false,
            ]);
            $subscription = $existing;
        } else {
            $subscription = BrandModuleSubscription::create([
                'brand_id' => $brandId,
                'module_id' => $module->id,
                'module_key' => $module->module_key,
                'module_name' => $module->name,
                'subscription_status' => 'active',
                'price' => $price,
                'currency_id' => $currencyId,
                'billing_cycle' => $data->billing_cycle,
                'subscription_start' => $periodDates['start'],
                'current_period_start' => $periodDates['start'],
                'current_period_end' => $periodDates['end'],
                'next_billing_at' => $periodDates['end'],
                'subscribed_at' => Carbon::now(),
            ]);
        }

        // Generate immediate invoice if requested
        $invoice = null;
        if ($data->immediate && $proration && $proration->net_amount > 0) {
            // Add the prorated charge to the next parent invoice or create one
            $invoice = $this->invoiceService->generateInvoice($parentSubscription);
        }

        return [
            'subscription' => $subscription,
            'proration' => $proration?->toArray(),
            'immediate_charge' => $proration?->net_amount ?? 0,
            'invoice' => $invoice,
        ];
    }

    /**
     * Unsubscribe from an add-on module.
     */
    public function unsubscribeFromModule(int $brandId, RemoveModuleData $data): array
    {
        $subscription = BrandModuleSubscription::where('brand_id', $brandId)
            ->where('module_id', $data->module_id)
            ->where('subscription_status', 'active')
            ->first();

        if (!$subscription) {
            throw new \InvalidArgumentException('Active module subscription not found');
        }

        $module = Module::find($data->module_id);

        if ($data->immediate) {
            // Calculate refund proration
            $proration = $this->calculateProration(
                $subscription->price,
                0,
                $subscription->current_period_start,
                $subscription->current_period_end,
            );

            // Update subscription
            $subscription->update([
                'subscription_status' => $data->refund ? 'canceled' : 'inactive',
                'canceled_at' => Carbon::now(),
            ]);

            // Create credit if refund requested
            if ($data->refund && $proration->credit_amount > 0) {
                $this->createAccountCredit($brandId, $proration->credit_amount, $subscription->currency_id);
            }

            return [
                'subscription' => $subscription,
                'proration' => $proration->toArray(),
                'refund_amount' => $data->refund ? $proration->credit_amount : 0,
                'immediate' => true,
            ];
        } else {
            // Cancel at period end
            $subscription->update([
                'cancel_at_period_end' => true,
            ]);

            return [
                'subscription' => $subscription,
                'cancels_at' => $subscription->current_period_end,
                'immediate' => false,
            ];
        }
    }

    /**
     * Get proration preview for adding a module.
     */
    public function previewProration(int $brandId, int $moduleId, string $billingCycle): ?ProrationResult
    {
        $module = Module::findOrFail($moduleId);
        $parentSubscription = PlanSubscription::where('brand_id', $brandId)
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if (!$parentSubscription || !$parentSubscription->current_period_start) {
            return null;
        }

        $price = $this->calculateModulePrice($module, $billingCycle);

        return $this->calculateProration(
            0,
            $price,
            $parentSubscription->current_period_start,
            $parentSubscription->current_period_end ?? Carbon::now()->addMonth(),
        );
    }

    /**
     * Calculate module price based on billing cycle.
     */
    private function calculateModulePrice(Module $module, string $billingCycle): float
    {
        $basePrice = $module->base_price;

        // Apply billing cycle multiplier
        $multiplier = match($billingCycle) {
            'monthly' => 1,
            'quarterly' => 3,
            'semi_annually' => 6,
            'annually' => 12 * 0.9, // 10% discount for annual
            'biennially' => 24 * 0.85, // 15% discount for biennial
            default => 1,
        };

        return round($basePrice * $multiplier, 2);
    }

    /**
     * Calculate period dates.
     */
    private function calculatePeriodDates(string $billingCycle, PlanSubscription $parentSubscription): array
    {
        $start = Carbon::now();
        
        // Align with parent subscription if possible
        if ($parentSubscription->current_period_start) {
            $start = Carbon::parse($parentSubscription->current_period_start);
        }

        $end = match($billingCycle) {
            'monthly' => $start->copy()->addMonth(),
            'quarterly' => $start->copy()->addMonths(3),
            'semi_annually' => $start->copy()->addMonths(6),
            'annually' => $start->copy()->addYear(),
            'biennially' => $start->copy()->addYears(2),
            default => $start->copy()->addMonth(),
        };

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Calculate proration.
     */
    private function calculateProration(float $currentPrice, float $newPrice, Carbon $periodStart, Carbon $periodEnd): ProrationResult
    {
        return $this->prorationCalculator->calculate(
            currentPrice: $currentPrice,
            newPrice: $newPrice,
            periodStart: $periodStart,
            periodEnd: $periodEnd,
            description: 'Module add-on proration',
        );
    }

    /**
     * Create account credit.
     */
    private function createAccountCredit(int $brandId, float $amount, ?int $currencyId): void
    {
        $profile = \Modules\Subscription\Entities\BrandBillingProfile::firstOrCreate(
            ['brand_id' => $brandId],
            ['currency_code' => 'USD', 'default_gateway' => 'stripe']
        );

        $profile->addCredit($amount);
    }
}
