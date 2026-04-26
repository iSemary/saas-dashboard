<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Listeners;

use Modules\ProjectManagement\Domain\Events\MilestoneCompleted;
use Modules\ProjectManagement\Domain\Strategies\Notification\NotificationStrategyInterface;
use Modules\ProjectManagement\Domain\Entities\ProjectMember;

class NotifyOnMilestoneCompleted
{
    public function __construct(
        private NotificationStrategyInterface $notificationStrategy
    ) {}

    public function handle(MilestoneCompleted $event): void
    {
        $members = ProjectMember::where('project_id', $event->projectId)->get();

        foreach ($members as $member) {
            $this->notificationStrategy->notify(
                $member->user_id,
                'milestone.completed',
                [
                    'milestone_id' => $event->milestoneId,
                    'project_id' => $event->projectId,
                ]
            );
        }
    }
}
