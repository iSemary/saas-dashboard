<?php

namespace Modules\Geography\DTOs;

use Modules\Geography\Http\Requests\StoreTownRequest;

readonly class CreateTownData
{
    public function __construct(
        public string $name,
        public int $city_id,
    ) {}

    public static function fromRequest(StoreTownRequest $request): self
    {
        return new self(...$request->validated());
    }
}
