<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\LeaveBalance;

class LeaveBalanceRepository implements LeaveBalanceRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = LeaveBalance::query()->with(['employee', 'leaveType']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['leave_type_id'])) {
            $query->where('leave_type_id', $filters['leave_type_id']);
        }

        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        return $query->orderBy('year', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): LeaveBalance
    {
        return LeaveBalance::with(['employee', 'leaveType'])->findOrFail($id);
    }

    public function create(array $data): LeaveBalance
    {
        return LeaveBalance::create($data);
    }

    public function update(int $id, array $data): LeaveBalance
    {
        $balance = $this->findOrFail($id);
        $balance->update($data);
        return $balance->fresh();
    }

    public function delete(int $id): bool
    {
        return LeaveBalance::destroy($id) > 0;
    }

    public function getBalanceForEmployee(int $employeeId, int $leaveTypeId, int $year): ?LeaveBalance
    {
        return LeaveBalance::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->first();
    }

    public function getAllBalancesForEmployee(int $employeeId, int $year): array
    {
        return LeaveBalance::with('leaveType')
            ->where('employee_id', $employeeId)
            ->where('year', $year)
            ->get()
            ->toArray();
    }

    public function deductDays(int $id, float $days): bool
    {
        $balance = $this->findOrFail($id);
        $balance->used += $days;
        $balance->remaining = $balance->allocated + $balance->accrued + $balance->carried_over - $balance->used;
        return $balance->save();
    }

    public function addDays(int $id, float $days): bool
    {
        $balance = $this->findOrFail($id);
        $balance->accrued += $days;
        $balance->remaining = $balance->allocated + $balance->accrued + $balance->carried_over - $balance->used;
        return $balance->save();
    }
}
