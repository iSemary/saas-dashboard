<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Listeners;

use Modules\ProjectManagement\Domain\Events\TaskAssigned;
use Modules\ProjectManagement\Domain\Strategies\Notification\NotificationStrategyInterface;

class NotifyOnTaskAssigned
{
    public function __construct(
        private NotificationStrategyInterface $notificationStrategy
    ) {}

    public function handle(TaskAssigned $event): void
    {
        if ($event->assigneeId === null) {
            return;
        }

        $this->notificationStrategy->notify(
            $event->assigneeId,
            'task.assigned',
            [
                'task_id' => $event->taskId,
                'project_id' => $event->projectId,
            ]
        );
    }
}
