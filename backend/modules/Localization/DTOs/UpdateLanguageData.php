<?php

namespace Modules\Localization\DTOs;

use Modules\Localization\Http\Requests\UpdateLanguageRequest;

readonly class UpdateLanguageData
{
    public function __construct(
        public ?string $name = null,
        public ?string $code = null,
        public ?string $direction = null,
        public ?bool $is_active = null,
        public ?bool $is_default = null,
    ) {}

    public static function fromRequest(UpdateLanguageRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'code' => $this->code,
            'direction' => $this->direction,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
        ], fn ($value) => $value !== null);
    }
}
