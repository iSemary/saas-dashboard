<?php

namespace Modules\Utilities\DTOs;

use Modules\Utilities\Http\Requests\StoreCurrencyRequest;

readonly class CreateCurrencyData
{
    public function __construct(
        public string $name,
        public string $code,
        public ?string $symbol,
        public ?bool $is_active,
    ) {}

    public static function fromRequest(StoreCurrencyRequest $request): self
    {
        return new self(...$request->validated());
    }
}
