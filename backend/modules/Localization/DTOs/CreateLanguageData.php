<?php

namespace Modules\Localization\DTOs;

use Modules\Localization\Http\Requests\StoreLanguageRequest;

readonly class CreateLanguageData
{
    public function __construct(
        public string $name,
        public string $code,
        public ?string $direction,
        public ?bool $is_active,
        public ?bool $is_default,
    ) {}

    public static function fromRequest(StoreLanguageRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'direction' => $this->direction ?? 'ltr',
            'is_active' => $this->is_active ?? true,
            'is_default' => $this->is_default ?? false,
        ];
    }
}
