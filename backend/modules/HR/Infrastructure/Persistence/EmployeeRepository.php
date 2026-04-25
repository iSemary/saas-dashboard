<?php

namespace Modules\HR\Infrastructure\Persistence;

use App\Repositories\Traits\TableListTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Employee;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    use TableListTrait;

    public function __construct(protected Employee $model) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['department', 'position', 'manager']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['position_id'])) {
            $query->where('position_id', $filters['position_id']);
        }

        if (!empty($filters['employment_status'])) {
            $query->where('employment_status', $filters['employment_status']);
        }

        if (!empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
        }

        if (!empty($filters['manager_id'])) {
            $query->where('manager_id', $filters['manager_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Employee
    {
        return $this->model->with([
            'department', 'position', 'manager', 'subordinates',
            'documents', 'contracts', 'employmentHistory', 'creator'
        ])->findOrFail($id);
    }

    public function find(int $id): ?Employee
    {
        return $this->model->with(['department', 'position', 'manager'])->find($id);
    }

    public function findByUserId(int $userId): ?Employee
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function findByEmployeeNumber(string $employeeNumber): ?Employee
    {
        return $this->model->where('employee_number', $employeeNumber)->first();
    }

    public function create(array $data): Employee
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Employee
    {
        $employee = $this->findOrFail($id);
        $employee->update($data);
        return $employee->fresh();
    }

    public function delete(int $id): bool
    {
        $employee = $this->findOrFail($id);
        return $employee->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    public function getSubordinates(int $managerId): array
    {
        return $this->model->where('manager_id', $managerId)
            ->with(['department', 'position'])
            ->get()
            ->toArray();
    }

    public function getByDepartment(int $departmentId): array
    {
        return $this->model->where('department_id', $departmentId)
            ->whereIn('employment_status', ['active', 'probation', 'on_leave'])
            ->get()
            ->toArray();
    }

    public function getOrgChart(): array
    {
        $employees = $this->model->with(['department', 'position'])
            ->whereIn('employment_status', ['active', 'probation', 'on_leave'])
            ->get();

        return $this->buildOrgTree($employees);
    }

    private function buildOrgTree($employees, $managerId = null): array
    {
        $tree = [];
        foreach ($employees as $employee) {
            if ($employee->manager_id === $managerId) {
                $children = $this->buildOrgTree($employees, $employee->id);
                $emp = $employee->toArray();
                if ($children) {
                    $emp['subordinates'] = $children;
                }
                $tree[] = $emp;
            }
        }
        return $tree;
    }
}
