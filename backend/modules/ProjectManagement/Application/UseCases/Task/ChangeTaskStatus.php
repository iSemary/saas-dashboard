<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Application\UseCases\Task;

use Modules\ProjectManagement\Domain\ValueObjects\TaskStatus;
use Modules\ProjectManagement\Domain\Entities\Task;
use Modules\ProjectManagement\Infrastructure\Persistence\TaskRepositoryInterface;

class ChangeTaskStatus
{
    public function __construct(
        private TaskRepositoryInterface $repository
    ) {}

    public function execute(string $taskId, TaskStatus $newStatus): Task
    {
        $task = $this->repository->findOrFail($taskId);
        $task->transitionStatus($newStatus);
        return $task->fresh();
    }
}
