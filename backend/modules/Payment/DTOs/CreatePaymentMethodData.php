<?php

namespace Modules\Payment\DTOs;

use Modules\Payment\Http\Requests\StorePaymentMethodRequest;

readonly class CreatePaymentMethodData
{
    public function __construct(
        public string $name,
        public ?string $slug,
        public ?bool $is_active,
    ) {}

    public static function fromRequest(StorePaymentMethodRequest $request): self
    {
        return new self(...$request->validated());
    }
}
