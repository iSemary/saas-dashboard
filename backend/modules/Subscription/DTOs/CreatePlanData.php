<?php

namespace Modules\Subscription\DTOs;

use Modules\Subscription\Http\Requests\StorePlanRequest;

readonly class CreatePlanData
{
    public function __construct(
        public string $name,
        public ?string $slug,
        public ?string $description,
        public float $price,
        public ?string $currency,
        public ?string $billing_period,
        public ?bool $is_active,
    ) {}

    public static function fromRequest(StorePlanRequest $request): self
    {
        return new self(...$request->validated());
    }
}
