<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs;

class BudgetData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?int $fiscal_year_id = null,
        public readonly ?int $department_id = null,
        public readonly ?string $status = null,
        public readonly ?float $total_amount = null,
        public readonly ?string $description = null,
        public readonly ?string $start_date = null,
        public readonly ?string $end_date = null,
        public readonly ?array $custom_fields = null,
        public readonly ?array $items = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($v) => !is_null($v));
    }
}
