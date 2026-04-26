<?php

namespace Modules\Subscription\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Modules\Customer\Entities\BrandModuleSubscription;
use Modules\Subscription\DTOs\CreateInvoiceData;
use Modules\Subscription\Entities\PlanSubscription;
use Modules\Subscription\Entities\SubscriptionInvoice;
use Modules\Subscription\Entities\SubscriptionInvoiceItem;

class InvoiceGenerationService
{
    public function __construct(
        private ProrationCalculator $prorationCalculator,
    ) {}

    /**
     * Generate a new invoice for a subscription.
     */
    public function generateInvoice(PlanSubscription $subscription, ?Carbon $invoiceDate = null): SubscriptionInvoice
    {
        $invoiceDate = $invoiceDate ?? Carbon::now();
        
        // Calculate line items
        $lineItems = $this->buildLineItems($subscription, $invoiceDate);
        
        // Calculate totals
        $subtotal = collect($lineItems)->sum('amount');
        $taxAmount = $this->calculateTax($subtotal, $subscription->country_code);
        $discountAmount = $this->calculateDiscounts($subscription, $subtotal);
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        // Create invoice
        $invoice = SubscriptionInvoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'brand_id' => $subscription->brand_id,
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'plan_id' => $subscription->plan_id,
            'currency_id' => $subscription->currency_id,
            'country_code' => $subscription->country_code,
            'invoice_type' => 'subscription',
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'line_items' => $lineItems,
            'period_start' => $subscription->starts_at,
            'period_end' => $subscription->ends_at,
            'invoice_date' => $invoiceDate,
            'due_date' => $invoiceDate->copy()->addDays(7),
            'status' => 'pending',
        ]);

        // Create invoice items
        foreach ($lineItems as $item) {
            SubscriptionInvoiceItem::create([
                'invoice_id' => $invoice->id,
                'line_type' => $item['line_type'],
                'reference_type' => $item['reference_type'] ?? null,
                'reference_id' => $item['reference_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'],
                'amount' => $item['amount'],
                'tax_amount' => 0,
                'total_amount' => $item['amount'],
                'currency_id' => $subscription->currency_id,
                'period_start' => $item['period_start'] ?? null,
                'period_end' => $item['period_end'] ?? null,
                'metadata' => $item['metadata'] ?? null,
            ]);
        }

        return $invoice->fresh(['items', 'currency']);
    }

    /**
     * Preview upcoming invoice without saving.
     */
    public function previewUpcomingInvoice(PlanSubscription $subscription, ?Carbon $invoiceDate = null): array
    {
        $invoiceDate = $invoiceDate ?? Carbon::now();
        
        $lineItems = $this->buildLineItems($subscription, $invoiceDate);
        $subtotal = collect($lineItems)->sum('amount');
        $taxAmount = $this->calculateTax($subtotal, $subscription->country_code);
        $discountAmount = $this->calculateDiscounts($subscription, $subtotal);
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'line_items' => $lineItems,
            'period_start' => $subscription->starts_at?->toDateString(),
            'period_end' => $subscription->ends_at?->toDateString(),
            'due_date' => $invoiceDate->copy()->addDays(7)->toDateString(),
            'currency_code' => $subscription->currency?->code ?? 'USD',
        ];
    }

    /**
     * Build line items for an invoice.
     */
    private function buildLineItems(PlanSubscription $subscription, Carbon $invoiceDate): array
    {
        $items = [];

        // Plan base charge
        $items[] = [
            'line_type' => 'plan',
            'reference_type' => PlanSubscription::class,
            'reference_id' => $subscription->id,
            'description' => "Plan: {$subscription->plan->name} ({$subscription->billing_cycle})",
            'quantity' => 1,
            'unit_price' => $subscription->price,
            'amount' => $subscription->price,
            'period_start' => $subscription->starts_at?->toDateString(),
            'period_end' => $subscription->ends_at?->toDateString(),
        ];

        // Add-on modules
        $activeModules = BrandModuleSubscription::where('brand_id', $subscription->brand_id)
            ->where('subscription_status', 'active')
            ->whereNotNull('price')
            ->get();

        foreach ($activeModules as $module) {
            $items[] = [
                'line_type' => 'module',
                'reference_type' => BrandModuleSubscription::class,
                'reference_id' => $module->id,
                'description' => "Add-on: {$module->module_key} ({$module->billing_cycle})",
                'quantity' => 1,
                'unit_price' => $module->price,
                'amount' => $module->price,
                'period_start' => $module->current_period_start?->toDateString(),
                'period_end' => $module->current_period_end?->toDateString(),
            ];
        }

        return $items;
    }

    /**
     * Calculate tax (placeholder - integrate with tax service).
     */
    private function calculateTax(float $amount, ?string $countryCode): float
    {
        // TODO: Integrate with TaxJar, Avalara, or similar
        return 0;
    }

    /**
     * Calculate applicable discounts.
     */
    private function calculateDiscounts(PlanSubscription $subscription, float $subtotal): float
    {
        $discount = 0;

        // Apply plan discounts
        $planDiscounts = $subscription->plan->getActiveDiscounts();
        foreach ($planDiscounts as $planDiscount) {
            if ($planDiscount->discount_type === 'percentage') {
                $discount += $subtotal * ($planDiscount->discount_value / 100);
            } else {
                $discount += $planDiscount->discount_value;
            }
        }

        return round($discount, 2);
    }

    /**
     * Generate unique invoice number.
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = Carbon::now()->format('Ymd');
        $random = Str::upper(Str::random(6));
        
        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Mark invoice as paid.
     */
    public function markAsPaid(SubscriptionInvoice $invoice, ?string $paymentId = null): void
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);
    }

    /**
     * Mark invoice as failed.
     */
    public function markAsFailed(SubscriptionInvoice $invoice, string $reason): void
    {
        $invoice->update([
            'status' => $invoice->status === 'pending' ? 'overdue' : $invoice->status,
            'notes' => $invoice->notes ? $invoice->notes . "\nFailed: {$reason}" : "Failed: {$reason}",
        ]);
    }

    /**
     * Void an invoice.
     */
    public function voidInvoice(SubscriptionInvoice $invoice, string $reason): void
    {
        if ($invoice->status === 'paid') {
            throw new \InvalidArgumentException('Cannot void a paid invoice. Use refund instead.');
        }

        $invoice->update([
            'status' => 'void',
            'voided_at' => Carbon::now(),
            'notes' => $invoice->notes ? $invoice->notes . "\nVoided: {$reason}" : "Voided: {$reason}",
        ]);
    }

    /**
     * Generate invoices for all subscriptions due for billing.
     */
    public function generateDueInvoices(): array
    {
        $results = [
            'generated' => 0,
            'errors' => [],
        ];

        $subscriptions = PlanSubscription::where('status', 'active')
            ->where('next_billing_at', '<=', Carbon::now()->addDay())
            ->get();

        foreach ($subscriptions as $subscription) {
            try {
                $this->generateInvoice($subscription);
                $results['generated']++;
                
                // Advance billing date
                $subscription->update([
                    'next_billing_at' => $this->calculateNextBillingDate($subscription),
                ]);
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Calculate next billing date based on cycle.
     */
    private function calculateNextBillingDate(PlanSubscription $subscription): Carbon
    {
        $interval = match($subscription->billing_cycle) {
            'monthly' => 1,
            'quarterly' => 3,
            'semi_annually' => 6,
            'annually' => 12,
            'biennially' => 24,
            default => 1,
        };

        return Carbon::parse($subscription->next_billing_at ?? $subscription->ends_at)
            ->addMonthsNoOverflow($interval);
    }
}
