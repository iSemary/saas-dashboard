<?php

namespace Modules\Customer\DTOs;

use Modules\Customer\Http\Requests\StoreBranchRequest;

readonly class CreateBranchData
{
    public function __construct(
        public string $name,
        public ?string $slug = null,
        public ?int $brand_id = null,
        public ?bool $is_active = null,
    ) {}

    public static function fromRequest(StoreBranchRequest $request): self
    {
        return new self(...$request->validated());
    }
}
