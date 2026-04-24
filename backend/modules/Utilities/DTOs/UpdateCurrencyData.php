<?php

namespace Modules\Utilities\DTOs;

use Modules\Utilities\Http\Requests\UpdateCurrencyRequest;

readonly class UpdateCurrencyData
{
    public function __construct(
        public ?string $name = null,
        public ?string $code = null,
        public ?string $symbol = null,
        public ?bool $is_active = null,
    ) {}

    public static function fromRequest(UpdateCurrencyRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'code' => $this->code,
            'symbol' => $this->symbol,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
