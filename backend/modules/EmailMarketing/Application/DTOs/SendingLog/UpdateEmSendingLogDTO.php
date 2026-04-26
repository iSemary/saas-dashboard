<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\DTOs\SendingLog;

class UpdateEmSendingLogDTO
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $failed_reason = null,
        public readonly ?array $metadata = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
