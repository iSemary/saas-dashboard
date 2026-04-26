<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\ChartOfAccount;

class UpdateChartOfAccountDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $type = null,
        public readonly ?string $sub_type = null,
        public readonly ?int $parent_id = null,
        public readonly ?int $level = null,
        public readonly ?bool $is_active = null,
        public readonly ?bool $is_leaf = null,
        public readonly ?bool $reconcile = null,
        public readonly ?float $opening_balance = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
