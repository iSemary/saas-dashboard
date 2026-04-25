<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\LeaveType;

interface LeaveTypeRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): LeaveType;
    public function create(array $data): LeaveType;
    public function update(int $id, array $data): LeaveType;
    public function delete(int $id): bool;
    public function getActiveLeaveTypes(): array;
}
