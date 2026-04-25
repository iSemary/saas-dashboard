<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Goal;

class GoalRepository implements GoalRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Goal::query()->with(['employee', 'manager', 'performanceCycle', 'keyResults']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['performance_cycle_id'])) {
            $query->where('performance_cycle_id', $filters['performance_cycle_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('due_date', 'asc')->paginate($perPage);
    }

    public function findOrFail(int $id): Goal
    {
        return Goal::with(['employee', 'manager', 'keyResults'])->findOrFail($id);
    }

    public function create(array $data): Goal
    {
        return Goal::create($data);
    }

    public function update(int $id, array $data): Goal
    {
        $goal = $this->findOrFail($id);
        $goal->update($data);
        return $goal->fresh();
    }

    public function delete(int $id): bool
    {
        return Goal::destroy($id) > 0;
    }

    public function getByEmployee(int $employeeId): array
    {
        return Goal::with('keyResults')
            ->where('employee_id', $employeeId)
            ->orderBy('due_date', 'asc')
            ->get()
            ->toArray();
    }

    public function getActiveByEmployee(int $employeeId): array
    {
        return Goal::with('keyResults')
            ->where('employee_id', $employeeId)
            ->active()
            ->orderBy('due_date', 'asc')
            ->get()
            ->toArray();
    }
}
