<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\ImportJob;

class UpdateSmImportJobDTO
{
    public function __construct(
        public readonly ?array $column_mapping = null,
        public readonly ?string $status = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
