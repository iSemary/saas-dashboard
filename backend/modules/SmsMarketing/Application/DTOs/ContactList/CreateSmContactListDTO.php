<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\ContactList;

class CreateSmContactListDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly string $status = 'active',
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
