<?php

namespace Modules\Email\DTOs;

use Modules\Email\Http\Requests\StoreEmailCredentialRequest;

readonly class CreateEmailCredentialData
{
    public function __construct(
        public string $name,
        public ?string $description,
        public string $from_address,
        public string $from_name,
        public string $mailer,
        public string $host,
        public int $port,
        public ?string $username,
        public ?string $password,
        public ?string $encryption,
        public ?string $status,
    ) {}

    public static function fromRequest(StoreEmailCredentialRequest $request): self
    {
        return new self(...$request->validated());
    }
}
