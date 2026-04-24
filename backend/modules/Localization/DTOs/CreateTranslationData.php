<?php

namespace Modules\Localization\DTOs;

use Modules\Localization\Http\Requests\StoreTranslationRequest;

readonly class CreateTranslationData
{
    public function __construct(
        public string $key,
        public string $value,
        public ?string $group,
        public int $language_id,
    ) {}

    public static function fromRequest(StoreTranslationRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
            'group' => $this->group,
            'language_id' => $this->language_id,
        ];
    }
}
