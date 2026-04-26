<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Attendance;
use Carbon\Carbon;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Attendance::query()->with(['employee', 'approvedBy']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
        }

        if (!empty($filters['is_approved'])) {
            $query->where('is_approved', $filters['is_approved']);
        }

        if (!empty($filters['search'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('first_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('last_name', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('date', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Attendance
    {
        return Attendance::with(['employee', 'approvedBy'])->findOrFail($id);
    }

    public function create(array $data): Attendance
    {
        return Attendance::create($data);
    }

    public function update(int $id, array $data): Attendance
    {
        $attendance = $this->findOrFail($id);
        $attendance->update($data);
        return $attendance->fresh();
    }

    public function delete(int $id): bool
    {
        return Attendance::destroy($id) > 0;
    }

    public function getTodayAttendance(int $employeeId): ?Attendance
    {
        return Attendance::where('employee_id', $employeeId)
            ->where('date', Carbon::today())
            ->first();
    }

    public function getAttendanceByDateRange(int $employeeId, string $startDate, string $endDate): array
    {
        return Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();
    }

    public function getPendingApprovals(): array
    {
        return Attendance::with('employee')
            ->where('is_approved', false)
            ->whereNotNull('check_in')
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();
    }

    public function bulkDelete(array $ids): bool
    {
        return Attendance::whereIn('id', $ids)->delete() > 0;
    }

    public function getCountByStatus(): \Illuminate\Support\Collection
    {
        return Attendance::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();
    }
}
