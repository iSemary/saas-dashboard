<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\Budget;

class UpdateBudgetDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $department = null,
        public readonly ?string $status = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
