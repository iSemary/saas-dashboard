<?php

namespace App\Services\BulkAction;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class BulkActionService
{
    protected array $actions = [];
    protected array $entityConfig = [];

    public function registerAction(string $id, BulkActionInterface $action): void
    {
        $this->actions[$id] = $action;
    }

    public function registerEntityConfig(string $entity, array $config): void
    {
        $this->entityConfig[$entity] = $config;
    }

    public function getAvailableActions(string $entity): array
    {
        $actions = [];
        
        foreach ($this->actions as $id => $action) {
            if ($action->isAvailable($entity)) {
                $actions[] = [
                    'id' => $action->getId(),
                    'label' => $action->getLabel(),
                    'requires_confirmation' => $action->requiresConfirmation(),
                    'confirmation_message' => $action->getConfirmationMessage(),
                ];
            }
        }

        return $actions;
    }

    public function execute(string $actionId, string $entity, array $ids, array $params = [], int $userId = 0): BulkActionResult
    {
        if (!isset($this->actions[$actionId])) {
            return new BulkActionResult(
                success: false,
                errors: ["Action '{$actionId}' not found"]
            );
        }

        $action = $this->actions[$actionId];

        if (!$action->isAvailable($entity)) {
            return new BulkActionResult(
                success: false,
                errors: ["Action '{$actionId}' is not available for entity '{$entity}'"]
            );
        }

        // Process in batches to avoid memory issues
        $result = new BulkActionResult();
        $batchSize = 100;
        $batches = array_chunk($ids, $batchSize);

        foreach ($batches as $batchIds) {
            $batchResult = $action->execute($batchIds, $params, $userId);
            
            $result->processedCount += $batchResult->processedCount;
            $result->successCount += $batchResult->successCount;
            $result->errorCount += $batchResult->errorCount;
            $result->errors = array_merge($result->errors, $batchResult->errors);
            $result->data = array_merge($result->data, $batchResult->data);
        }

        $result->success = empty($result->errors);

        return $result;
    }

    /**
     * Preview what would happen without actually executing
     */
    public function preview(string $actionId, string $entity, array $ids): BulkActionResult
    {
        // For now, just return a preview result
        // This could be enhanced to show specific changes per action
        return new BulkActionResult(
            success: true,
            processedCount: count($ids),
            data: ['ids' => $ids]
        );
    }

    /**
     * Export selected IDs to Excel/CSV
     */
    public function export(string $entity, array $ids, string $format = 'xlsx'): array
    {
        $config = $this->entityConfig[$entity] ?? [];
        $modelClass = $config['model'] ?? null;
        
        if (!$modelClass || !class_exists($modelClass)) {
            return [
                'success' => false,
                'errors' => ["Model not configured for entity: {$entity}"]
            ];
        }

        $records = $modelClass::whereIn('id', $ids)->get();
        
        return [
            'success' => true,
            'count' => $records->count(),
            'data' => $records->toArray(),
        ];
    }
}
