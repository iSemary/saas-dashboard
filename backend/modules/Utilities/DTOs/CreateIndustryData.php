<?php

namespace Modules\Utilities\DTOs;

use Modules\Utilities\Http\Requests\StoreIndustryRequest;

readonly class CreateIndustryData
{
    public function __construct(
        public string $name,
        public ?string $slug,
    ) {}

    public static function fromRequest(StoreIndustryRequest $request): self
    {
        return new self(...$request->validated());
    }
}
