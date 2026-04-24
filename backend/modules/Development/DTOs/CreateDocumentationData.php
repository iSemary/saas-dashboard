<?php

namespace Modules\Development\DTOs;

use Modules\Development\Http\Requests\StoreDocumentationRequest;

readonly class CreateDocumentationData
{
    public function __construct(
        public string $title,
        public ?string $slug,
        public ?string $body,
        public ?string $category,
        public ?bool $is_published,
    ) {}

    public static function fromRequest(StoreDocumentationRequest $request): self
    {
        return new self(...$request->validated());
    }
}
