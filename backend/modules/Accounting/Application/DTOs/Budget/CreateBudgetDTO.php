<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\Budget;

class CreateBudgetDTO
{
    public function __construct(
        public readonly string $name,
        public readonly int $fiscal_year_id,
        public readonly ?string $description = null,
        public readonly ?string $department = null,
        public readonly string $status = 'draft',
        public readonly string $currency = 'USD',
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
