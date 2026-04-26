<?php

namespace Modules\Development\DTOs;

use Modules\Development\Http\Requests\UpdateFeatureFlagRequest;

readonly class UpdateFeatureFlagData
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?bool $is_active = null,
    ) {}

    public static function fromRequest(UpdateFeatureFlagRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_enabled' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
