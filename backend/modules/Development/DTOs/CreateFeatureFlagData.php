<?php

namespace Modules\Development\DTOs;

use Modules\Development\Http\Requests\StoreFeatureFlagRequest;

readonly class CreateFeatureFlagData
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public ?bool $is_enabled,
    ) {}

    public static function fromRequest(StoreFeatureFlagRequest $request): self
    {
        return new self(...$request->validated());
    }
}
