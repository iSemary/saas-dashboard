<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\JournalEntry;

class CreateJournalEntryDTO
{
    public function __construct(
        public readonly string $entry_number,
        public readonly string $entry_date,
        public readonly string $state = 'draft',
        public readonly ?string $reference = null,
        public readonly ?string $description = null,
        public readonly ?int $fiscal_year_id = null,
        public readonly string $currency = 'USD',
        public readonly ?array $custom_fields = null,
        public readonly array $items = [],
    ) {}

    public function toArray(): array
    {
        $data = array_filter(get_object_vars($this), fn ($value, $key) => $value !== null && $key !== 'items', ARRAY_FILTER_USE_BOTH);
        return $data;
    }
}
