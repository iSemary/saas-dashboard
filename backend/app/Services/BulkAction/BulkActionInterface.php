<?php

namespace App\Services\BulkAction;

use App\Services\BulkAction\BulkActionResult;

interface BulkActionInterface
{
    /**
     * Get the action identifier
     */
    public function getId(): string;

    /**
     * Get the action label
     */
    public function getLabel(): string;

    /**
     * Check if this action requires confirmation
     */
    public function requiresConfirmation(): bool;

    /**
     * Get confirmation message if confirmation is required
     */
    public function getConfirmationMessage(): ?string;

    /**
     * Execute the bulk action on the given IDs
     * @param array $ids Array of entity IDs
     * @param array $params Additional parameters for the action
     * @param int $userId The ID of the user performing the action
     * @return BulkActionResult
     */
    public function execute(array $ids, array $params, int $userId): BulkActionResult;

    /**
     * Check if this action is available for the given entity
     */
    public function isAvailable(string $entity): bool;
}
