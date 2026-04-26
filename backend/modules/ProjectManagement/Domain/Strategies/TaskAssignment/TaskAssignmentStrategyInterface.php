<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\TaskAssignment;

interface TaskAssignmentStrategyInterface
{
    public function assign(string $taskId, ?string $assigneeId): void;
}
