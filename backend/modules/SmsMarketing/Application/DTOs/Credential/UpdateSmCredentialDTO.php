<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\Credential;

class UpdateSmCredentialDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $provider = null,
        public readonly ?string $account_sid = null,
        public readonly ?string $auth_token = null,
        public readonly ?string $from_number = null,
        public readonly ?string $webhook_url = null,
        public readonly ?bool $is_default = null,
        public readonly ?string $status = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
