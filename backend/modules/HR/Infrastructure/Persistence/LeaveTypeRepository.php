<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\LeaveType;

class LeaveTypeRepository implements LeaveTypeRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = LeaveType::query();

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['is_paid'])) {
            $query->where('is_paid', $filters['is_paid']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function findOrFail(int $id): LeaveType
    {
        return LeaveType::findOrFail($id);
    }

    public function create(array $data): LeaveType
    {
        return LeaveType::create($data);
    }

    public function update(int $id, array $data): LeaveType
    {
        $leaveType = $this->findOrFail($id);
        $leaveType->update($data);
        return $leaveType->fresh();
    }

    public function delete(int $id): bool
    {
        return LeaveType::destroy($id) > 0;
    }

    public function getActiveLeaveTypes(): array
    {
        return LeaveType::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}
