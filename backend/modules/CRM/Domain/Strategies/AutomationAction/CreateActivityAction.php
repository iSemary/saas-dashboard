<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\AutomationAction;

use Illuminate\Database\Eloquent\Model;
use Modules\CRM\Domain\Entities\Activity;
use Modules\CRM\Domain\Exceptions\AutomationExecutionException;

/**
 * Create an activity related to the record.
 */
class CreateActivityAction implements AutomationActionStrategyInterface
{
    public function supports(string $actionType): bool
    {
        return $actionType === 'create_activity';
    }

    public function execute(array $actionConfig, object $model, array $context): void
    {
        if (!$model instanceof Model) {
            throw new AutomationExecutionException('Model must be an Eloquent instance');
        }

        $subject = $actionConfig['subject'] ?? 'Automated activity';
        $type = $actionConfig['type'] ?? 'task';
        $description = $actionConfig['description'] ?? null;
        $dueDate = $actionConfig['due_date'] ?? null;
        $assignedTo = $actionConfig['assigned_to'] ?? $model->assigned_to ?? auth()->id();

        Activity::create([
            'subject' => $subject,
            'type' => $type,
            'description' => $description,
            'related_type' => get_class($model),
            'related_id' => $model->id,
            'assigned_to' => $assignedTo,
            'created_by' => auth()->id(),
            'due_date' => $dueDate,
            'status' => 'planned',
        ]);
    }

    public function getName(): string
    {
        return 'Create Activity';
    }

    public function getDescription(): string
    {
        return 'Create a follow-up activity related to this record.';
    }

    public function getConfigFields(): array
    {
        return [
            [
                'name' => 'subject',
                'type' => 'string',
                'required' => true,
                'label' => 'Activity Subject',
            ],
            [
                'name' => 'type',
                'type' => 'select',
                'required' => true,
                'label' => 'Activity Type',
                'options' => ['call', 'email', 'meeting', 'task'],
            ],
            [
                'name' => 'description',
                'type' => 'textarea',
                'required' => false,
                'label' => 'Description',
            ],
            [
                'name' => 'due_date',
                'type' => 'datetime',
                'required' => false,
                'label' => 'Due Date',
                'help' => 'Relative to now (e.g., +1 day, +1 week). Leave empty for today.',
            ],
        ];
    }
}
