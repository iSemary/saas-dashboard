<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\BankTransaction;

class CreateBankTransactionDTO
{
    public function __construct(
        public readonly int $bank_account_id,
        public readonly string $transaction_date,
        public readonly string $type = 'debit',
        public readonly float $amount = 0,
        public readonly ?string $description = null,
        public readonly ?string $reference = null,
        public readonly string $source = 'manual',
        public readonly ?array $raw_data = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
