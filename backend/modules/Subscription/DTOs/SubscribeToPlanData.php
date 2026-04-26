<?php

namespace Modules\Subscription\DTOs;

use Modules\Subscription\Http\Requests\Tenant\SubscribeToPlanRequest;

readonly class SubscribeToPlanData
{
    public function __construct(
        public int $plan_id,
        public string $gateway,
        public int $currency_id,
        public ?string $billing_cycle = 'monthly',
        public ?string $success_url = null,
        public ?string $cancel_url = null,
        public ?int $payment_method_id = null,
        public ?array $metadata = [],
    ) {}

    public static function fromRequest(SubscribeToPlanRequest $request): self
    {
        return new self(...$request->validated());
    }
}
