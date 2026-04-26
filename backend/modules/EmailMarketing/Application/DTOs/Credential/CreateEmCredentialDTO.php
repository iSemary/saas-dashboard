<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\DTOs\Credential;

class CreateEmCredentialDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $provider,
        public readonly ?string $host = null,
        public readonly ?int $port = null,
        public readonly ?string $username = null,
        public readonly ?string $password = null,
        public readonly ?string $from_email = null,
        public readonly ?string $from_name = null,
        public readonly bool $is_default = false,
        public readonly string $status = 'active',
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
