<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\LeaveBalance;

interface LeaveBalanceRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): LeaveBalance;
    public function create(array $data): LeaveBalance;
    public function update(int $id, array $data): LeaveBalance;
    public function delete(int $id): bool;
    public function getBalanceForEmployee(int $employeeId, int $leaveTypeId, int $year): ?LeaveBalance;
    public function getAllBalancesForEmployee(int $employeeId, int $year): array;
    public function deductDays(int $id, float $days): bool;
    public function addDays(int $id, float $days): bool;
}
