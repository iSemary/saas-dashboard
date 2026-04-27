<?php

namespace App\Services\BulkAction;

class BulkActionResult
{
    public function __construct(
        public bool $success = true,
        public int $processedCount = 0,
        public int $successCount = 0,
        public int $errorCount = 0,
        public array $errors = [],
        public array $data = []
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'processed_count' => $this->processedCount,
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'errors' => $this->errors,
            'data' => $this->data,
        ];
    }

    public function addError(string $error): void
    {
        $this->errors[] = $error;
        $this->errorCount++;
        $this->success = false;
    }

    public function addSuccess(int $count = 1): void
    {
        $this->successCount += $count;
        $this->processedCount += $count;
    }
}
