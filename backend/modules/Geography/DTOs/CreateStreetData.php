<?php

namespace Modules\Geography\DTOs;

use Modules\Geography\Http\Requests\StoreStreetRequest;

readonly class CreateStreetData
{
    public function __construct(
        public string $name,
        public int $town_id,
    ) {}

    public static function fromRequest(StoreStreetRequest $request): self
    {
        return new self(...$request->validated());
    }
}
