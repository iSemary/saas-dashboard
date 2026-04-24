<?php

namespace Modules\Subscription\DTOs;

use Modules\Subscription\Http\Requests\UpdatePlanRequest;

readonly class UpdatePlanData
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?float $price = null,
        public ?string $currency = null,
        public ?string $billing_period = null,
        public ?bool $is_active = null,
    ) {}

    public static function fromRequest(UpdatePlanRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'billing_period' => $this->billing_period,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
