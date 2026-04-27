<?php

namespace App\Services\Import;

interface ImportAdapterInterface
{
    /**
     * Get the required headers for import
     * @return array<string>
     */
    public function getRequiredHeaders(): array;

    /**
     * Get all supported headers (including optional ones)
     * @return array<string>
     */
    public function getSupportedHeaders(): array;

    /**
     * Validate a single row
     * @param array $row The row data (key-value pairs)
     * @param int $rowNumber The row number for error reporting
     * @return array Array of error messages, empty if valid
     */
    public function validateRow(array $row, int $rowNumber): array;

    /**
     * Transform row data before import
     * @param array $row The raw row data
     * @return array The transformed data ready for database insertion
     */
    public function transformRow(array $row): array;

    /**
     * Import a single row
     * @param array $data The transformed row data
     * @param int $userId The ID of the user performing the import
     * @return int|null The ID of the created/updated record, or null on failure
     */
    public function importRow(array $data, int $userId): ?int;

    /**
     * Generate template data with sample row
     * @return array Array of rows for the template
     */
    public function getTemplateData(): array;

    /**
     * Get the entity name
     * @return string
     */
    public function getEntityName(): string;
}
