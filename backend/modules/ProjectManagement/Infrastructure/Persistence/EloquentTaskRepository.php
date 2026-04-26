<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Task;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    use TableListTrait;

    public function find(string $id): ?Task
    {
        return Task::find($id);
    }

    public function findOrFail(string $id): Task
    {
        return Task::findOrFail($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(string $id, array $data): Task
    {
        $task = $this->findOrFail($id);
        $task->update($data);
        return $task->fresh();
    }

    public function delete(string $id): bool
    {
        $task = $this->find($id);
        return $task ? $task->delete() : false;
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Task::query();

        if (!empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['assignee_id'])) {
            $query->where('assignee_id', $filters['assignee_id']);
        }

        if (!empty($filters['milestone_id'])) {
            $query->where('milestone_id', $filters['milestone_id']);
        }

        if (!empty($filters['board_column_id'])) {
            $query->where('board_column_id', $filters['board_column_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('position')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        $query = Task::query();

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }

        if (!empty($filters['overdue'])) {
            $query->where('due_date', '<', now())
                ->whereNotIn('status', ['done', 'cancelled']);
        }

        return $query->orderBy('position')->get()->all();
    }

    public function exists(string $id): bool
    {
        return Task::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = Task::query();

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['from'])) {
            $query->where('updated_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->where('updated_at', '<=', $filters['to']);
        }

        return $query->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = Task::query();
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }

    public function getWorkloadByAssignee(?string $tenantId = null): \Illuminate\Support\Collection
    {
        $query = Task::selectRaw('assignee_id, count(*) as task_count, sum(estimated_hours) as total_hours')
            ->whereNotNull('assignee_id')
            ->whereNotIn('status', ['done', 'cancelled']);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->groupBy('assignee_id')->get();
    }
}
