<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\DTOs\AbTest;

class CreateEmAbTestDTO
{
    public function __construct(
        public readonly int $campaign_id,
        public readonly string $variant_name,
        public readonly ?string $subject = null,
        public readonly ?string $body_html = null,
        public readonly int $percentage = 50,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
