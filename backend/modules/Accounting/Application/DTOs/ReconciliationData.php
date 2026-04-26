<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs;

class ReconciliationData
{
    public function __construct(
        public readonly ?int $bank_account_id = null,
        public readonly ?string $statement_date = null,
        public readonly ?float $statement_balance = null,
        public readonly ?float $book_balance = null,
        public readonly ?float $difference = null,
        public readonly ?string $status = null,
        public readonly ?string $description = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($v) => !is_null($v));
    }
}
