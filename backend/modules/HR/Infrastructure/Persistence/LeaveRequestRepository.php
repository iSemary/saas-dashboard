<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\LeaveRequest;

class LeaveRequestRepository implements LeaveRequestRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = LeaveRequest::query()->with(['employee', 'leaveType', 'approvedBy']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['leave_type_id'])) {
            $query->where('leave_type_id', $filters['leave_type_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('start_date', [$filters['start_date'], $filters['end_date']]);
        }

        if (!empty($filters['search'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('first_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('last_name', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): LeaveRequest
    {
        return LeaveRequest::with(['employee', 'leaveType', 'approvedBy'])->findOrFail($id);
    }

    public function create(array $data): LeaveRequest
    {
        return LeaveRequest::create($data);
    }

    public function update(int $id, array $data): LeaveRequest
    {
        $request = $this->findOrFail($id);
        $request->update($data);
        return $request->fresh();
    }

    public function delete(int $id): bool
    {
        return LeaveRequest::destroy($id) > 0;
    }

    public function getPendingRequests(): array
    {
        return LeaveRequest::with(['employee', 'leaveType'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function getRequestsByEmployee(int $employeeId, ?string $status = null): array
    {
        $query = LeaveRequest::with('leaveType')->where('employee_id', $employeeId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('start_date', 'desc')->get()->toArray();
    }

    public function hasOverlappingLeave(int $employeeId, string $startDate, string $endDate, ?int $excludeId = null): bool
    {
        $query = LeaveRequest::where('employee_id', $employeeId)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function getPendingCountForApprover(int $approverId): int
    {
        return LeaveRequest::where('status', 'pending')->count();
    }

    public function getCountByStatus(): \Illuminate\Support\Collection
    {
        return LeaveRequest::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();
    }
}
