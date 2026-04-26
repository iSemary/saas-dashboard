<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\TaskAssignment;

class DefaultTaskAssignmentStrategy implements TaskAssignmentStrategyInterface
{
    public function assign(string $taskId, ?string $assigneeId): void
    {
        // Default: direct assignment, no special logic
    }
}
