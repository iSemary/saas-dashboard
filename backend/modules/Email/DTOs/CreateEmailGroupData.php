<?php

namespace Modules\Email\DTOs;

use Modules\Email\Http\Requests\StoreEmailGroupRequest;

readonly class CreateEmailGroupData
{
    public function __construct(
        public string $name,
        public ?string $description,
    ) {}

    public static function fromRequest(StoreEmailGroupRequest $request): self
    {
        return new self(...$request->validated());
    }
}
