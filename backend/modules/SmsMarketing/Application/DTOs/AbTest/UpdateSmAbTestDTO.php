<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\AbTest;

class UpdateSmAbTestDTO
{
    public function __construct(
        public readonly ?string $variant_name = null,
        public readonly ?string $body = null,
        public readonly ?int $percentage = null,
        public readonly ?string $winner = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
