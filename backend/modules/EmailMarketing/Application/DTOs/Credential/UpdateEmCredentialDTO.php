<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\DTOs\Credential;

class UpdateEmCredentialDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $provider = null,
        public readonly ?string $host = null,
        public readonly ?int $port = null,
        public readonly ?string $username = null,
        public readonly ?string $password = null,
        public readonly ?string $from_email = null,
        public readonly ?string $from_name = null,
        public readonly ?bool $is_default = null,
        public readonly ?string $status = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
