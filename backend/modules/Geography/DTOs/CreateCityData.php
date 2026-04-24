<?php

namespace Modules\Geography\DTOs;

use Modules\Geography\Http\Requests\StoreCityRequest;

readonly class CreateCityData
{
    public function __construct(
        public string $name,
        public int $province_id,
    ) {}

    public static function fromRequest(StoreCityRequest $request): self
    {
        return new self(...$request->validated());
    }
}
