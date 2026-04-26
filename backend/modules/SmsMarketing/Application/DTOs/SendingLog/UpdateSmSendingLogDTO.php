<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\SendingLog;

class UpdateSmSendingLogDTO
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $failed_reason = null,
        public readonly ?string $provider_message_id = null,
        public readonly ?float $cost = null,
        public readonly ?array $metadata = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
