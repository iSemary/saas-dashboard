<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\OptOut;

class UpdateSmOptOutDTO
{
    public function __construct(
        public readonly ?string $reason = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
