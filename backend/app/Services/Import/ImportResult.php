<?php

namespace App\Services\Import;

class ImportResult
{
    public function __construct(
        public int $successCount = 0,
        public int $errorCount = 0,
        public array $errors = [],
        public array $importedIds = []
    ) {}

    public function toArray(): array
    {
        return [
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'errors' => $this->errors,
            'imported_ids' => $this->importedIds,
        ];
    }
}
