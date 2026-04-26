<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\Credential;

class CreateSmCredentialDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $provider,
        public readonly ?string $account_sid = null,
        public readonly ?string $auth_token = null,
        public readonly ?string $from_number = null,
        public readonly ?string $webhook_url = null,
        public readonly bool $is_default = false,
        public readonly string $status = 'active',
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
