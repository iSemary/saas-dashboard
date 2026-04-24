<?php

namespace Modules\Geography\DTOs;

use Modules\Geography\Http\Requests\StoreCountryRequest;

readonly class CreateCountryData
{
    public function __construct(
        public string $name,
        public string $code,
        public ?string $phone_code,
        public ?bool $is_active,
    ) {}

    public static function fromRequest(StoreCountryRequest $request): self
    {
        return new self(...$request->validated());
    }
}
