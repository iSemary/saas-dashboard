<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\ChartOfAccount;

class CreateChartOfAccountDTO
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $type,
        public readonly ?string $description = null,
        public readonly ?string $sub_type = null,
        public readonly ?int $parent_id = null,
        public readonly int $level = 1,
        public readonly bool $is_active = true,
        public readonly bool $is_leaf = true,
        public readonly bool $reconcile = false,
        public readonly string $currency = 'USD',
        public readonly float $opening_balance = 0,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
