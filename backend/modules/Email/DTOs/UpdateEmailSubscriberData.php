<?php

namespace Modules\Email\DTOs;

use Modules\Email\Http\Requests\UpdateEmailSubscriberRequest;

readonly class UpdateEmailSubscriberData
{
    public function __construct(
        public ?string $email = null,
        public ?string $name = null,
        public ?bool $is_active = null,
    ) {}

    public static function fromRequest(UpdateEmailSubscriberRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'email' => $this->email,
            'name' => $this->name,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
