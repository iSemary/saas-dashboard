<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\Reconciliation;

class UpdateReconciliationDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $start_date = null,
        public readonly ?string $end_date = null,
        public readonly ?float $closing_balance = null,
        public readonly ?float $statement_balance = null,
        public readonly ?string $status = null,
        public readonly ?string $notes = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
