<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\BankTransaction;

class UpdateBankTransactionDTO
{
    public function __construct(
        public readonly ?string $transaction_date = null,
        public readonly ?string $type = null,
        public readonly ?float $amount = null,
        public readonly ?string $description = null,
        public readonly ?string $reference = null,
        public readonly ?string $status = null,
        public readonly ?int $journal_entry_id = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
