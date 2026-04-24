<?php

namespace Modules\Development\DTOs;

use Modules\Development\Http\Requests\UpdateDocumentationRequest;

readonly class UpdateDocumentationData
{
    public function __construct(
        public ?string $title = null,
        public ?string $slug = null,
        public ?string $body = null,
        public ?string $category = null,
        public ?bool $is_published = null,
    ) {}

    public static function fromRequest(UpdateDocumentationRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
            'category' => $this->category,
            'is_published' => $this->is_published,
        ], fn ($value) => $value !== null);
    }
}
