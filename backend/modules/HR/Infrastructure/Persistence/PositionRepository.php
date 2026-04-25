<?php

namespace Modules\HR\Infrastructure\Persistence;

use App\Repositories\Traits\TableListTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Position;

class PositionRepository implements PositionRepositoryInterface
{
    use TableListTrait;

    public function __construct(protected Position $model) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['department', 'employees']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Position
    {
        return $this->model->with(['department', 'employees'])->findOrFail($id);
    }

    public function find(int $id): ?Position
    {
        return $this->model->with(['department'])->find($id);
    }

    public function create(array $data): Position
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Position
    {
        $position = $this->findOrFail($id);
        $position->update($data);
        return $position->fresh();
    }

    public function delete(int $id): bool
    {
        $position = $this->findOrFail($id);
        return $position->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    public function getByDepartment(int $departmentId): array
    {
        return $this->model->where('department_id', $departmentId)
            ->where('is_active', true)
            ->get()
            ->toArray();
    }
}
