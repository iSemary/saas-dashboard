<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\AutomationAction;

use Illuminate\Database\Eloquent\Model;
use Modules\CRM\Domain\Exceptions\AutomationExecutionException;

/**
 * Update a field on the record.
 */
class UpdateFieldAction implements AutomationActionStrategyInterface
{
    public function supports(string $actionType): bool
    {
        return $actionType === 'update_field';
    }

    public function execute(array $actionConfig, object $model, array $context): void
    {
        if (!$model instanceof Model) {
            throw new AutomationExecutionException(translate('message.operation_failed'));
        }

        $field = $actionConfig['field'] ?? null;
        $value = $actionConfig['value'] ?? null;

        if ($field === null) {
            throw new AutomationExecutionException('Field name is required for update_field action');
        }

        if (!in_array($field, $model->getFillable(), true)) {
            throw new AutomationExecutionException("Field '{$field}' is not fillable on this model");
        }

        $model->update([$field => $value]);
    }

    public function getName(): string
    {
        return 'Update Field';
    }

    public function getDescription(): string
    {
        return 'Update a specific field on the record to a new value.';
    }

    public function getConfigFields(): array
    {
        return [
            [
                'name' => 'field',
                'type' => 'string',
                'required' => true,
                'label' => 'Field Name',
            ],
            [
                'name' => 'value',
                'type' => 'string',
                'required' => false,
                'label' => 'New Value',
            ],
        ];
    }
}
