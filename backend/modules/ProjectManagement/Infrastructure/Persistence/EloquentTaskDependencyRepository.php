<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\TaskDependency;
use Illuminate\Database\Eloquent\Collection;

class EloquentTaskDependencyRepository implements TaskDependencyRepositoryInterface
{
    public function find(string $id): ?TaskDependency
    {
        return TaskDependency::find($id);
    }

    public function findOrFail(string $id): TaskDependency
    {
        return TaskDependency::findOrFail($id);
    }

    public function create(array $data): TaskDependency
    {
        return TaskDependency::create($data);
    }

    public function delete(string $id): bool
    {
        $item = $this->find($id);
        return $item ? $item->delete() : false;
    }

    public function getByTask(string $taskId): Collection
    {
        return TaskDependency::where('predecessor_id', $taskId)
            ->orWhere('successor_id', $taskId)
            ->get();
    }
}
