<?php

namespace Modules\Development\DTOs;

use Modules\Development\Http\Requests\StoreConfigurationRequest;

readonly class CreateConfigurationData
{
    public function __construct(
        public string $key,
        public string $value,
        public ?string $type,
        public ?string $group,
        public ?string $description,
    ) {}

    public static function fromRequest(StoreConfigurationRequest $request): self
    {
        return new self(...$request->validated());
    }
}
