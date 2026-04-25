<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\AutomationAction;

use Illuminate\Database\Eloquent\Model;
use Modules\CRM\Domain\Exceptions\AutomationExecutionException;

/**
 * Assign a user to the record.
 */
class AssignUserAction implements AutomationActionStrategyInterface
{
    public function supports(string $actionType): bool
    {
        return $actionType === 'assign_user';
    }

    public function execute(array $actionConfig, object $model, array $context): void
    {
        if (!$model instanceof Model) {
            throw new AutomationExecutionException('Model must be an Eloquent instance');
        }

        $userId = $actionConfig['user_id'] ?? null;

        if ($userId === null) {
            // Try to get from context (e.g., current user who triggered)
            $userId = $context['triggered_by'] ?? auth()->id();
        }

        if ($userId === null) {
            throw new AutomationExecutionException('No user_id provided for assign_user action');
        }

        // Validate user exists
        $userExists = \DB::table('users')->where('id', $userId)->exists();
        if (!$userExists) {
            throw new AutomationExecutionException("User with ID {$userId} does not exist");
        }

        $model->update(['assigned_to' => $userId]);
    }

    public function getName(): string
    {
        return 'Assign User';
    }

    public function getDescription(): string
    {
        return 'Assign the record to a specific user or the user who triggered the automation.';
    }

    public function getConfigFields(): array
    {
        return [
            [
                'name' => 'user_id',
                'type' => 'number',
                'required' => false,
                'label' => 'User ID',
                'help' => 'Leave empty to assign to the user who triggered the automation.',
            ],
        ];
    }
}
