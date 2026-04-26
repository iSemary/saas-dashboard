<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\TaxRate;

class UpdateTaxRateDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?float $rate = null,
        public readonly ?string $type = null,
        public readonly ?string $description = null,
        public readonly ?bool $is_active = null,
        public readonly ?bool $is_compound = null,
        public readonly ?int $account_id = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
