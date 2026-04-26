<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\TaxRate;

class CreateTaxRateDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $code,
        public readonly float $rate,
        public readonly string $type = 'both',
        public readonly ?string $description = null,
        public readonly bool $is_active = true,
        public readonly bool $is_compound = false,
        public readonly ?int $account_id = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
