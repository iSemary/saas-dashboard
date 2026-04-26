<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\DTOs\ContactList;

class UpdateEmContactListDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $status = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
