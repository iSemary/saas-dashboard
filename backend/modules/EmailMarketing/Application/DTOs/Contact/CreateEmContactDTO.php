<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\DTOs\Contact;

class CreateEmContactDTO
{
    public function __construct(
        public readonly string $email,
        public readonly ?string $first_name = null,
        public readonly ?string $last_name = null,
        public readonly ?string $phone = null,
        public readonly ?array $custom_fields = null,
        public readonly string $status = 'active',
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
