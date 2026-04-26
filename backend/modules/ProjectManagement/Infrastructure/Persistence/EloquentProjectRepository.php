<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Project;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentProjectRepository implements ProjectRepositoryInterface
{
    use TableListTrait;

    public function find(string $id): ?Project
    {
        return Project::find($id);
    }

    public function findOrFail(string $id): Project
    {
        return Project::findOrFail($id);
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function update(string $id, array $data): Project
    {
        $project = $this->findOrFail($id);
        $project->update($data);
        return $project->fresh();
    }

    public function delete(string $id): bool
    {
        $project = $this->find($id);
        return $project ? $project->delete() : false;
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Project::query();

        if (!empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }

        if (!empty($filters['workspace_id'])) {
            $query->where('workspace_id', $filters['workspace_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['health'])) {
            $query->where('health', $filters['health']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        $query = Project::query();

        if (!empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        }

        return $query->orderBy('name')->get()->all();
    }

    public function exists(string $id): bool
    {
        return Project::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = Project::query();

        if (!empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = Project::query();
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }

    public function getHealthDistribution(?string $tenantId = null): \Illuminate\Support\Collection
    {
        $query = Project::selectRaw('health, count(*) as count');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->groupBy('health')->pluck('count', 'health');
    }
}
