<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs;

class TaxRateData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?float $rate = null,
        public readonly ?string $type = null,
        public readonly ?int $account_id = null,
        public readonly ?bool $is_active = null,
        public readonly ?string $description = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($v) => !is_null($v));
    }
}
