<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\Contact;

class CreateSmContactDTO
{
    public function __construct(
        public readonly string $phone,
        public readonly ?string $first_name = null,
        public readonly ?string $last_name = null,
        public readonly ?string $email = null,
        public readonly ?array $custom_fields = null,
        public readonly string $status = 'active',
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
