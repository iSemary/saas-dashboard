<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\Import;

/**
 * Interface for import strategies.
 * Each entity type that supports import implements this.
 */
interface ImportStrategyInterface
{
    /**
     * Check if this strategy supports the given entity type.
     */
    public function supports(string $entityType): bool;

    /**
     * Validate a single row of import data.
     *
     * @return array Array of error messages (empty if valid)
     */
    public function validateRow(array $row, array $mapping): array;

    /**
     * Process a single row and create/update the entity.
     *
     * @return bool True if successful
     */
    public function processRow(array $row, array $mapping, int $importJobId): bool;

    /**
     * Get the list of required fields for import.
     *
     * @return array Array of field names
     */
    public function getRequiredFields(): array;

    /**
     * Get available fields for mapping.
     *
     * @return array Array of field definitions with labels
     */
    public function getAvailableFields(): array;

    /**
     * Get default field mappings.
     *
     * @return array Column index => field name
     */
    public function getDefaultMapping(): array;

    /**
     * Get sample data for preview.
     *
     * @return array Sample row data
     */
    public function getSampleData(): array;

    /**
     * Get the entity name for display.
     */
    public function getEntityName(): string;

    /**
     * Check for duplicates based on unique fields.
     *
     * @return int|null Existing entity ID if duplicate found, null otherwise
     */
    public function checkDuplicate(array $row, array $mapping): ?int;
}
