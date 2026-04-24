<?php

namespace Modules\Email\DTOs;

use Modules\Email\Http\Requests\UpdateEmailCredentialRequest;

readonly class UpdateEmailCredentialData
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        public ?string $from_address = null,
        public ?string $from_name = null,
        public ?string $mailer = null,
        public ?string $host = null,
        public ?int $port = null,
        public ?string $username = null,
        public ?string $password = null,
        public ?string $encryption = null,
        public ?string $status = null,
    ) {}

    public static function fromRequest(UpdateEmailCredentialRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'from_address' => $this->from_address,
            'from_name' => $this->from_name,
            'mailer' => $this->mailer,
            'host' => $this->host,
            'port' => $this->port,
            'username' => $this->username,
            'password' => $this->password,
            'encryption' => $this->encryption,
            'status' => $this->status,
        ], fn ($value) => $value !== null);
    }
}
