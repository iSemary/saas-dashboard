<?php

namespace Modules\Tenant\DTOs;

use Modules\Tenant\Http\Requests\UpdateTenantRequest;

readonly class UpdateTenantData
{
    public function __construct(
        public ?string $name = null,
        public ?string $domain = null,
        public ?bool $is_active = null,
    ) {}

    public static function fromRequest(UpdateTenantRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'domain' => $this->domain,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
