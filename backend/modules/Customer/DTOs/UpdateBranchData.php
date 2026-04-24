<?php

namespace Modules\Customer\DTOs;

use Modules\Customer\Http\Requests\UpdateBranchRequest;

readonly class UpdateBranchData
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?int $brand_id = null,
        public ?bool $is_active = null,
    ) {}

    public static function fromRequest(UpdateBranchRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'slug' => $this->slug,
            'brand_id' => $this->brand_id,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
