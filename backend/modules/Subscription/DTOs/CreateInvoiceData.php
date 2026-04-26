<?php

namespace Modules\Subscription\DTOs;

readonly class CreateInvoiceData
{
    public function __construct(
        public int $brand_id,
        public int $subscription_id,
        public ?int $user_id = null,
        public int $plan_id,
        public int $currency_id,
        public string $country_code,
        public string $invoice_type = 'subscription',
        public float $subtotal = 0,
        public float $discount_amount = 0,
        public float $tax_amount = 0,
        public float $total_amount = 0,
        public ?string $period_start = null,
        public ?string $period_end = null,
        public ?string $due_date = null,
        public ?string $notes = null,
        public ?array $line_items = [],
        public ?array $applied_discounts = null,
        public ?array $tax_breakdown = null,
        public ?array $billing_address = null,
        public ?array $metadata = null,
    ) {}
}
