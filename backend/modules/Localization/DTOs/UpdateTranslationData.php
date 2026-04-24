<?php

namespace Modules\Localization\DTOs;

use Modules\Localization\Http\Requests\UpdateTranslationRequest;

readonly class UpdateTranslationData
{
    public function __construct(
        public ?string $key = null,
        public ?string $value = null,
        public ?string $group = null,
        public ?int $language_id = null,
    ) {}

    public static function fromRequest(UpdateTranslationRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'key' => $this->key,
            'value' => $this->value,
            'group' => $this->group,
            'language_id' => $this->language_id,
        ], fn ($value) => $value !== null);
    }
}
