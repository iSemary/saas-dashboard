<?php

namespace Modules\Development\DTOs;

use Modules\Development\Http\Requests\UpdateConfigurationRequest;

readonly class UpdateConfigurationData
{
    public function __construct(
        public ?string $key = null,
        public ?string $value = null,
        public ?string $type = null,
        public ?string $group = null,
        public ?string $description = null,
    ) {}

    public static function fromRequest(UpdateConfigurationRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'key' => $this->key,
            'value' => $this->value,
            'type' => $this->type,
            'group' => $this->group,
            'description' => $this->description,
        ], fn ($value) => $value !== null);
    }
}
