<?php

namespace App\Services\BulkAction\Actions;

use App\Services\BulkAction\BulkActionResult;

class DeactivateAction extends AbstractBulkAction
{
    public function __construct()
    {
        parent::__construct(
            'deactivate',
            'Deactivate',
            false,
            null
        );
    }

    protected function getModelClass(): string
    {
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

        $record->status = 'inactive';
        $record->is_active = false;

        return $record->save();
    }

    public function execute(array $ids, array $params, int $userId): BulkActionResult
    {
        if (isset($params['model_class'])) {
            $this->setModelClass($params['model_class']);
        }

        if (empty($this->getModelClass())) {
            return new BulkActionResult(
                success: false,
                errors: ['Model class not specified for deactivate action']
            );
        }

        return parent::execute($ids, $params, $userId);
    }

    private string $modelClass = '';
}
