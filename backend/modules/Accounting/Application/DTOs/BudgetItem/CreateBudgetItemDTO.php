<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\BudgetItem;

class CreateBudgetItemDTO
{
    public function __construct(
        public readonly int $budget_id,
        public readonly int $account_id,
        public readonly float $planned_amount = 0,
        public readonly float $actual_amount = 0,
        public readonly ?string $notes = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
