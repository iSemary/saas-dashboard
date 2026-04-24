<?php

namespace Modules\FileManager\DTOs;

use Modules\FileManager\Http\Requests\UpdateFolderRequest;

readonly class UpdateFolderData
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        public ?int $parent_id = null,
        public ?string $status = null,
    ) {}

    public static function fromRequest(UpdateFolderRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'status' => $this->status,
        ], fn ($value) => $value !== null);
    }
}
