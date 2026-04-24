<?php

namespace Modules\Email\DTOs;

use Modules\Email\Http\Requests\UpdateEmailRecipientRequest;

readonly class UpdateEmailRecipientData
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?int $group_id = null,
    ) {}

    public static function fromRequest(UpdateEmailRecipientRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'group_id' => $this->group_id,
        ], fn ($value) => $value !== null);
    }
}
