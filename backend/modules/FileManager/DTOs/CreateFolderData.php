<?php

namespace Modules\FileManager\DTOs;

use Modules\FileManager\Http\Requests\StoreFolderRequest;

readonly class CreateFolderData
{
    public function __construct(
        public string $name,
        public ?string $description,
        public ?int $parent_id,
        public ?string $status,
    ) {}

    public static function fromRequest(StoreFolderRequest $request): self
    {
        return new self(...$request->validated());
    }
}
