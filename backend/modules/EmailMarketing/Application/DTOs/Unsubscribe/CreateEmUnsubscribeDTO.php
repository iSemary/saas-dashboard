<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\DTOs\Unsubscribe;

class CreateEmUnsubscribeDTO
{
    public function __construct(
        public readonly int $contact_id,
        public readonly ?int $campaign_id = null,
        public readonly ?string $reason = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
