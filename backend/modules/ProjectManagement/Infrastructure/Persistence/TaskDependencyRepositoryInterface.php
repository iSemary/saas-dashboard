<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\TaskDependency;
use Illuminate\Database\Eloquent\Collection;

interface TaskDependencyRepositoryInterface
{
    public function find(string $id): ?TaskDependency;
    public function findOrFail(string $id): TaskDependency;
    public function create(array $data): TaskDependency;
    public function delete(string $id): bool;
    public function getByTask(string $taskId): Collection;
}
