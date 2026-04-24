<?php

namespace Modules\Customer\DTOs;

use Modules\Customer\Http\Requests\StoreBrandRequest;

readonly class CreateBrandData
{
    public function __construct(
        public string $name,
        public ?string $slug,
        public ?string $domain,
        public ?string $logo,
        public ?bool $is_active,
        public ?array $modules = null,
    ) {}

    public static function fromRequest(StoreBrandRequest $request): self
    {
        return new self(...$request->validated());
    }
}
