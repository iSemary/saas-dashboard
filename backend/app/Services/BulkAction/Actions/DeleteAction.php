<?php

namespace App\Services\BulkAction\Actions;

use App\Services\BulkAction\BulkActionResult;

class DeleteAction extends AbstractBulkAction
{
    public function __construct()
    {
        parent::__construct(
            'delete',
            'Delete',
            true,
            'Are you sure you want to delete the selected items? This action cannot be undone.'
        );
    }

    protected function getModelClass(): string
    {
        // This will be set dynamically via setModelClass or from entity config
        return $this->modelClass ?? '';
    }

    public function setModelClass(string $modelClass): void
    {
        $this->modelClass = $modelClass;
    }

    protected function executeSingle(int $id, array $params, int $userId): bool
    {
        $modelClass = $this->getModelClass();
        $record = $modelClass::find($id);

        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    public function execute(array $ids, array $params, int $userId): BulkActionResult
    {
        // Get model class from params or fallback
        if (isset($params['model_class'])) {
            $this->setModelClass($params['model_class']);
        }

        if (empty($this->getModelClass())) {
            return new BulkActionResult(
                success: false,
                errors: ['Model class not specified for delete action']
            );
        }

        return parent::execute($ids, $params, $userId);
    }

    private string $modelClass = '';
}
