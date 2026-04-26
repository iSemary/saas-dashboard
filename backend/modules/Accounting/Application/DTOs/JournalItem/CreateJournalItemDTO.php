<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\JournalItem;

class CreateJournalItemDTO
{
    public function __construct(
        public readonly int $journal_entry_id,
        public readonly int $account_id,
        public readonly float $debit = 0,
        public readonly float $credit = 0,
        public readonly ?string $description = null,
        public readonly ?string $reference = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
