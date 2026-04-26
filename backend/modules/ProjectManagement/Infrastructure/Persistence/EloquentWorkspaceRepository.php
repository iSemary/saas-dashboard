<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Workspace;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentWorkspaceRepository implements WorkspaceRepositoryInterface
{
    use TableListTrait;

    public function find(string $id): ?Workspace
    {
        return Workspace::find($id);
    }

    public function findOrFail(string $id): Workspace
    {
        return Workspace::findOrFail($id);
    }

    public function create(array $data): Workspace
    {
        return Workspace::create($data);
    }

    public function update(string $id, array $data): Workspace
    {
        $workspace = $this->findOrFail($id);
        $workspace->update($data);
        return $workspace->fresh();
    }

    public function delete(string $id): bool
    {
        $workspace = $this->find($id);
        return $workspace ? $workspace->delete() : false;
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Workspace::query();

        if (!empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return Workspace::query()
            ->when(!empty($filters['tenant_id']), fn($q) => $q->where('tenant_id', $filters['tenant_id']))
            ->orderBy('name')
            ->get()
            ->all();
    }

    public function count(array $filters = []): int
    {
        return Workspace::query()
            ->when(!empty($filters['tenant_id']), fn($q) => $q->where('tenant_id', $filters['tenant_id']))
            ->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = Workspace::query();
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
