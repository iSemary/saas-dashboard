<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs;

class BudgetItemData
{
    public function __construct(
        public readonly ?int $budget_id = null,
        public readonly ?int $account_id = null,
        public readonly ?float $amount = null,
        public readonly ?string $description = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($v) => !is_null($v));
    }
}
