<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\AbTest;

class CreateSmAbTestDTO
{
    public function __construct(
        public readonly int $campaign_id,
        public readonly string $variant_name,
        public readonly ?string $body = null,
        public readonly int $percentage = 50,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
