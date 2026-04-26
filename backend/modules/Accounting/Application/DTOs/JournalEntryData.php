<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs;

class JournalEntryData
{
    public function __construct(
        public readonly ?string $entry_number = null,
        public readonly ?string $entry_date = null,
        public readonly ?string $state = null,
        public readonly ?string $reference = null,
        public readonly ?string $description = null,
        public readonly ?string $currency = null,
        public readonly ?int $fiscal_year_id = null,
        public readonly ?array $custom_fields = null,
        public readonly ?array $items = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($v) => !is_null($v));
    }
}
