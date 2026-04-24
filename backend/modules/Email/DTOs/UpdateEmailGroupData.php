<?php

namespace Modules\Email\DTOs;

use Modules\Email\Http\Requests\UpdateEmailGroupRequest;

readonly class UpdateEmailGroupData
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
    ) {}

    public static function fromRequest(UpdateEmailGroupRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
        ], fn ($value) => $value !== null);
    }
}
