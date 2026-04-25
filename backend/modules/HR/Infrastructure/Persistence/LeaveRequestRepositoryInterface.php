<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\LeaveRequest;

interface LeaveRequestRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): LeaveRequest;
    public function create(array $data): LeaveRequest;
    public function update(int $id, array $data): LeaveRequest;
    public function delete(int $id): bool;
    public function getPendingRequests(): array;
    public function getRequestsByEmployee(int $employeeId, ?string $status = null): array;
    public function hasOverlappingLeave(int $employeeId, string $startDate, string $endDate, ?int $excludeId = null): bool;
    public function getPendingCountForApprover(int $approverId): int;
}
