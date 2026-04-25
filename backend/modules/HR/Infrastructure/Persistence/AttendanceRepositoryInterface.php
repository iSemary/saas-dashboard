<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Attendance;

interface AttendanceRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Attendance;
    public function create(array $data): Attendance;
    public function update(int $id, array $data): Attendance;
    public function delete(int $id): bool;
    public function getTodayAttendance(int $employeeId): ?Attendance;
    public function getAttendanceByDateRange(int $employeeId, string $startDate, string $endDate): array;
    public function getPendingApprovals(): array;
    public function bulkDelete(array $ids): bool;
}
