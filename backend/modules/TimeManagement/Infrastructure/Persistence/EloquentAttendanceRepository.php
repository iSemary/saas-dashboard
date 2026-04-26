<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\Attendance;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentAttendanceRepository implements AttendanceRepositoryInterface
{
    public function find(string $id): ?Attendance
    {
        return Attendance::find($id);
    }

    public function findOrFail(string $id): Attendance
    {
        return Attendance::findOrFail($id);
    }

    public function create(array $data): Attendance
    {
        return Attendance::create($data);
    }

    public function update(string $id, array $data): Attendance
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(string $id): bool
    {
        $item = $this->find($id);
        return $item ? $item->delete() : false;
    }

    public function paginateByUser(string $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Attendance::where('user_id', $userId)
            ->when($filters['date_from'] ?? null, fn($q, $d) => $q->where('date', '>=', $d))
            ->when($filters['date_to'] ?? null, fn($q, $d) => $q->where('date', '<=', $d))
            ->orderBy('date', 'desc');

        return $query->paginate($perPage);
    }

    public function findActiveClockIn(string $userId, string $date): ?Attendance
    {
        return Attendance::where('user_id', $userId)
            ->where('date', $date)
            ->whereNull('clock_out')
            ->firstOrFail();
    }
}
