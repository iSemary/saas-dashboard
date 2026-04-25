<?php

namespace Modules\HR\Infrastructure\Persistence;

use App\Repositories\Traits\TableListTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Department;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    use TableListTrait;

    public function __construct(protected Department $model) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['parent', 'manager', 'employees']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Department
    {
        return $this->model->with(['parent', 'manager', 'subDepartments', 'employees'])->findOrFail($id);
    }

    public function find(int $id): ?Department
    {
        return $this->model->with(['parent', 'manager'])->find($id);
    }

    public function create(array $data): Department
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Department
    {
        $department = $this->findOrFail($id);
        $department->update($data);
        return $department->fresh();
    }

    public function delete(int $id): bool
    {
        $department = $this->findOrFail($id);
        return $department->deleteWithCheck();
    }

    public function bulkDelete(array $ids): int
    {
        $count = 0;
        foreach ($ids as $id) {
            try {
                if ($this->delete($id)) {
                    $count++;
                }
            } catch (\Exception $e) {
                // Skip departments that can't be deleted
            }
        }
        return $count;
    }

    public function getTree(): array
    {
        $departments = $this->model->with(['manager'])->get();
        return $this->buildTree($departments);
    }

    public function getByParent(?int $parentId): array
    {
        return $this->model->where('parent_id', $parentId)->get()->toArray();
    }

    private function buildTree($departments, $parentId = null): array
    {
        $tree = [];
        foreach ($departments as $department) {
            if ($department->parent_id === $parentId) {
                $children = $this->buildTree($departments, $department->id);
                $dept = $department->toArray();
                if ($children) {
                    $dept['children'] = $children;
                }
                $tree[] = $dept;
            }
        }
        return $tree;
    }
}
