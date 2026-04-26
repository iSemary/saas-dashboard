<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Application\UseCases\Task;

use Modules\ProjectManagement\Application\DTOs\CreateTaskData;
use Modules\ProjectManagement\Domain\Entities\Task;
use Modules\ProjectManagement\Infrastructure\Persistence\TaskRepositoryInterface;

class CreateTask
{
    public function __construct(
        private TaskRepositoryInterface $repository
    ) {}

    public function execute(CreateTaskData $data, string $userId): Task
    {
        $taskData = $data->toArray();
        $taskData['created_by'] = $userId;

        return $this->repository->create($taskData);
    }
}
