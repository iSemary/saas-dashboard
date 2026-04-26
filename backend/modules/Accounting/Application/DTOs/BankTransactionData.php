<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs;

class BankTransactionData
{
    public function __construct(
        public readonly ?int $bank_account_id = null,
        public readonly ?string $date = null,
        public readonly ?string $type = null,
        public readonly ?float $amount = null,
        public readonly ?string $description = null,
        public readonly ?string $reference = null,
        public readonly ?int $reconciliation_id = null,
        public readonly ?int $journal_item_id = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($v) => !is_null($v));
    }
}
