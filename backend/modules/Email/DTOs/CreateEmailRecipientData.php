<?php

namespace Modules\Email\DTOs;

use Modules\Email\Http\Requests\StoreEmailRecipientRequest;

readonly class CreateEmailRecipientData
{
    public function __construct(
        public ?string $name,
        public string $email,
        public ?int $group_id,
    ) {}

    public static function fromRequest(StoreEmailRecipientRequest $request): self
    {
        return new self(...$request->validated());
    }
}
