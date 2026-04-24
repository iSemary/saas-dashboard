<?php

namespace Modules\Customer\DTOs;

use Modules\Customer\Http\Requests\UpdateBrandRequest;

readonly class UpdateBrandData
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $domain = null,
        public ?bool $is_active = null,
    ) {}

    public static function fromRequest(UpdateBrandRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'slug' => $this->slug,
            'domain' => $this->domain,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
