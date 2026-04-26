<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\BudgetItem;

class UpdateBudgetItemDTO
{
    public function __construct(
        public readonly ?int $account_id = null,
        public readonly ?float $planned_amount = null,
        public readonly ?float $actual_amount = null,
        public readonly ?string $notes = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
