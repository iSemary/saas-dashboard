<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\DTOs\ImportJob;

class CreateEmImportJobDTO
{
    public function __construct(
        public readonly int $contact_list_id,
        public readonly string $file_path,
        public readonly ?array $column_mapping = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
