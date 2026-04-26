<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\DTOs\FiscalYear;

class CreateFiscalYearDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly bool $is_active = false,
        public readonly ?string $description = null,
        public readonly ?array $custom_fields = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
