<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\Reconciliation;

class CreateReconciliationDTO
{
    public function __construct(
        public readonly int $bank_account_id,
        public readonly string $name,
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly float $opening_balance = 0,
        public readonly float $closing_balance = 0,
        public readonly float $statement_balance = 0,
        public readonly ?string $notes = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
