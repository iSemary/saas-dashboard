<?php

namespace Modules\Geography\DTOs;

use Modules\Geography\Http\Requests\UpdateCountryRequest;

readonly class UpdateCountryData
{
    public function __construct(
        public ?string $name = null,
        public ?string $code = null,
        public ?string $phone_code = null,
        public ?bool $is_active = null,
    ) {}

    public static function fromRequest(UpdateCountryRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'code' => $this->code,
            'phone_code' => $this->phone_code,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
