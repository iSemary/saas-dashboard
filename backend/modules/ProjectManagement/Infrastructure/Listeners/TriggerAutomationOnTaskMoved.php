<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Listeners;

use Modules\ProjectManagement\Domain\Events\TaskMovedToColumn;
use Modules\ProjectManagement\Domain\Strategies\AutomationAction\AutomationActionStrategyInterface;

class TriggerAutomationOnTaskMoved
{
    public function __construct(
        private AutomationActionStrategyInterface $automationStrategy
    ) {}

    public function handle(TaskMovedToColumn $event): void
    {
        $this->automationStrategy->execute('task.moved_to_column', [
            'task_id' => $event->taskId,
            'project_id' => $event->projectId,
            'from_column_id' => $event->fromColumnId,
            'to_column_id' => $event->toColumnId,
        ]);
    }
}
