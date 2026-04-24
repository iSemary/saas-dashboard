<?php

namespace Modules\Customer\DTOs;

use Modules\Customer\Http\Requests\StoreBranchRequest;

readonly class CreateBranchData
{
    public function __construct(
        public string $name,
        public ?string $slug,
        public ?int $brand_id,
        public ?bool $is_active,
    ) {}

    public static function fromRequest(StoreBranchRequest $request): self
    {
        return new self(...$request->validated());
    }
}
