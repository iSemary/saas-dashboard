<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs;

class JournalItemData
{
    public function __construct(
        public readonly ?int $journal_entry_id = null,
        public readonly ?int $account_id = null,
        public readonly ?float $debit = null,
        public readonly ?float $credit = null,
        public readonly ?string $description = null,
        public readonly ?string $reference = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($v) => !is_null($v));
    }
}
