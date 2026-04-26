<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\BankAccount;

class CreateBankAccountDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $account_number = null,
        public readonly ?string $bank_name = null,
        public readonly ?string $branch = null,
        public readonly ?string $swift_code = null,
        public readonly ?string $iban = null,
        public readonly string $currency = 'USD',
        public readonly float $opening_balance = 0,
        public readonly ?int $gl_account_id = null,
        public readonly bool $is_active = true,
        public readonly ?string $notes = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
