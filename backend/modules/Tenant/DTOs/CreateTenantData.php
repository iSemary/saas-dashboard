<?php

namespace Modules\Tenant\DTOs;

use Modules\Tenant\Http\Requests\StoreTenantRequest;

readonly class CreateTenantData
{
    public function __construct(
        public string $name,
        public string $domain,
        public ?string $database_name,
        public ?bool $is_active,
    ) {}

    public static function fromRequest(StoreTenantRequest $request): self
    {
        return new self(...$request->validated());
    }
}
