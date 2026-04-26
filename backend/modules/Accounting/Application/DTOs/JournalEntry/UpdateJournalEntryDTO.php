<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\JournalEntry;

class UpdateJournalEntryDTO
{
    public function __construct(
        public readonly ?string $entry_date = null,
        public readonly ?string $reference = null,
        public readonly ?string $description = null,
        public readonly ?int $fiscal_year_id = null,
        public readonly ?array $custom_fields = null,
        public readonly ?array $items = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value, $key) => $value !== null && $key !== 'items', ARRAY_FILTER_USE_BOTH);
    }
}
