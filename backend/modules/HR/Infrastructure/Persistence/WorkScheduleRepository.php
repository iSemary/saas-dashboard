<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\WorkSchedule;
use Carbon\Carbon;

class WorkScheduleRepository implements WorkScheduleRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = WorkSchedule::query()->with(['employee', 'shift']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['shift_id'])) {
            $query->where('shift_id', $filters['shift_id']);
        }

        return $query->orderBy('effective_from', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): WorkSchedule
    {
        return WorkSchedule::with(['employee', 'shift'])->findOrFail($id);
    }

    public function create(array $data): WorkSchedule
    {
        return WorkSchedule::create($data);
    }

    public function update(int $id, array $data): WorkSchedule
    {
        $schedule = $this->findOrFail($id);
        $schedule->update($data);
        return $schedule->fresh();
    }

    public function delete(int $id): bool
    {
        return WorkSchedule::destroy($id) > 0;
    }

    public function getCurrentScheduleForEmployee(int $employeeId): ?WorkSchedule
    {
        $today = Carbon::today();
        
        return WorkSchedule::with('shift')
            ->where('employee_id', $employeeId)
            ->where('effective_from', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('effective_to')
                      ->orWhere('effective_to', '>=', $today);
            })
            ->orderBy('effective_from', 'desc')
            ->first();
    }

    public function getSchedulesByEmployee(int $employeeId): array
    {
        return WorkSchedule::with('shift')
            ->where('employee_id', $employeeId)
            ->orderBy('effective_from', 'desc')
            ->get()
            ->toArray();
    }
}
