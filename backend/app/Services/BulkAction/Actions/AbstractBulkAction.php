<?php

namespace App\Services\BulkAction\Actions;

use App\Services\BulkAction\BulkActionInterface;
use App\Services\BulkAction\BulkActionResult;

abstract class AbstractBulkAction implements BulkActionInterface
{
    protected string $id;
    protected string $label;
    protected bool $requiresConfirmation = false;
    protected ?string $confirmationMessage = null;
    protected array $supportedEntities = [];

    public function __construct(
        string $id,
        string $label,
        bool $requiresConfirmation = false,
        ?string $confirmationMessage = null
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->requiresConfirmation = $requiresConfirmation;
        $this->confirmationMessage = $confirmationMessage;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function requiresConfirmation(): bool
    {
        return $this->requiresConfirmation;
    }

    public function getConfirmationMessage(): ?string
    {
        return $this->confirmationMessage;
    }

    public function isAvailable(string $entity): bool
    {
        if (empty($this->supportedEntities)) {
            return true; // Available for all entities by default
        }
        
        return in_array($entity, $this->supportedEntities);
    }

    protected function setSupportedEntities(array $entities): void
    {
        $this->supportedEntities = $entities;
    }

    /**
     * Execute the action on a single record
     * Override this in specific action classes
     */
    abstract protected function executeSingle(int $id, array $params, int $userId): bool;

    /**
     * Get the model class for the entity
     */
    abstract protected function getModelClass(): string;

    public function execute(array $ids, array $params, int $userId): BulkActionResult
    {
        $result = new BulkActionResult();
        $modelClass = $this->getModelClass();
        
        foreach ($ids as $id) {
            try {
                // Verify record exists
                $record = $modelClass::find($id);
                if (!$record) {
                    $result->addError("Record {$id} not found");
                    continue;
                }

                if ($this->executeSingle($id, $params, $userId)) {
                    $result->addSuccess(1);
                } else {
                    $result->addError("Failed to process record {$id}");
                }
            } catch (\Exception $e) {
                $result->addError("Record {$id}: {$e->getMessage()}");
            }
        }

        return $result;
    }
}
