<?php

namespace Modules\Email\DTOs;

use Modules\Email\Http\Requests\StoreEmailSubscriberRequest;

readonly class CreateEmailSubscriberData
{
    public function __construct(
        public string $email,
        public ?string $name,
        public ?bool $is_active,
    ) {}

    public static function fromRequest(StoreEmailSubscriberRequest $request): self
    {
        return new self(...$request->validated());
    }
}
