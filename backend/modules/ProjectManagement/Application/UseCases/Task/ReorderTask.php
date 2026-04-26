<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Application\UseCases\Task;

use Modules\ProjectManagement\Domain\Entities\Task;
use Modules\ProjectManagement\Domain\Strategies\TaskPosition\TaskPositionStrategyInterface;
use Modules\ProjectManagement\Infrastructure\Persistence\TaskRepositoryInterface;

class ReorderTask
{
    public function __construct(
        private TaskRepositoryInterface $repository,
        private TaskPositionStrategyInterface $positionStrategy,
    ) {}

    public function execute(string $taskId, string $columnId, ?string $beforeTaskId = null, ?string $afterTaskId = null): Task
    {
        $task = $this->repository->findOrFail($taskId);

        $position = $this->positionStrategy->calculatePosition($columnId, $beforeTaskId, $afterTaskId);

        $task->board_column_id = $columnId;
        $task->position = $position;
        $task->save();

        return $task->fresh();
    }
}
