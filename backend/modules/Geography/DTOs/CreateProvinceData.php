<?php

namespace Modules\Geography\DTOs;

use Modules\Geography\Http\Requests\StoreProvinceRequest;

readonly class CreateProvinceData
{
    public function __construct(
        public string $name,
        public ?string $code,
        public int $country_id,
    ) {}

    public static function fromRequest(StoreProvinceRequest $request): self
    {
        return new self(...$request->validated());
    }
}
