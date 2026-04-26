<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Application\UseCases\Task;

use Modules\ProjectManagement\Application\DTOs\UpdateTaskData;
use Modules\ProjectManagement\Domain\Entities\Task;
use Modules\ProjectManagement\Infrastructure\Persistence\TaskRepositoryInterface;

class UpdateTask
{
    public function __construct(
        private TaskRepositoryInterface $repository
    ) {}

    public function execute(string $taskId, UpdateTaskData $data): Task
    {
        return $this->repository->update($taskId, $data->toArray());
    }
}
