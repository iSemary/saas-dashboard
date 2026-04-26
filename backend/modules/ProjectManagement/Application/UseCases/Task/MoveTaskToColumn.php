<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Application\UseCases\Task;

use Modules\ProjectManagement\Domain\Entities\Task;
use Modules\ProjectManagement\Domain\Exceptions\WipLimitExceeded;
use Modules\ProjectManagement\Domain\Strategies\BoardColumn\BoardColumnStrategyInterface;
use Modules\ProjectManagement\Infrastructure\Persistence\TaskRepositoryInterface;

class MoveTaskToColumn
{
    public function __construct(
        private TaskRepositoryInterface $repository,
        private BoardColumnStrategyInterface $boardColumnStrategy,
    ) {}

    public function execute(string $taskId, string $columnId): Task
    {
        $task = $this->repository->findOrFail($taskId);

        $currentCount = Task::where('board_column_id', $columnId)
            ->where('project_id', $task->project_id)
            ->count();

        $column = \Modules\ProjectManagement\Domain\Entities\BoardColumn::find($columnId);

        if (!$this->boardColumnStrategy->enforceWipLimit($columnId, $currentCount, $column?->wip_limit)) {
            throw new WipLimitExceeded($column?->name ?? $columnId, $column?->wip_limit ?? 0);
        }

        $task->moveToColumn($columnId);
        return $task->fresh();
    }
}
