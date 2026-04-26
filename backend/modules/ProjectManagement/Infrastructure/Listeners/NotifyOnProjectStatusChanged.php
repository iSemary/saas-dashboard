<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Listeners;

use Modules\ProjectManagement\Domain\Events\ProjectStatusChanged;
use Modules\ProjectManagement\Domain\Strategies\Notification\NotificationStrategyInterface;
use Modules\ProjectManagement\Domain\Entities\ProjectMember;
use Modules\ProjectManagement\Domain\Strategies\AutomationAction\AutomationActionStrategyInterface;

class NotifyOnProjectStatusChanged
{
    public function __construct(
        private NotificationStrategyInterface $notificationStrategy,
        private AutomationActionStrategyInterface $automationStrategy
    ) {}

    public function handle(ProjectStatusChanged $event): void
    {
        $members = ProjectMember::where('project_id', $event->projectId)->get();

        foreach ($members as $member) {
            $this->notificationStrategy->notify(
                $member->user_id,
                'project.status_changed',
                [
                    'project_id' => $event->projectId,
                    'from' => $event->from->value,
                    'to' => $event->to->value,
                ]
            );
        }

        $this->automationStrategy->execute('project.status_changed', [
            'project_id' => $event->projectId,
            'from' => $event->from->value,
            'to' => $event->to->value,
        ]);
    }
}
