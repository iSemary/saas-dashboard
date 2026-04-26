<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\SendingLog;

class CreateSmSendingLogDTO
{
    public function __construct(
        public readonly int $campaign_id,
        public readonly int $contact_id,
        public readonly string $status = 'queued',
        public readonly ?array $metadata = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
