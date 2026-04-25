<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\WorkSchedule;

interface WorkScheduleRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): WorkSchedule;
    public function create(array $data): WorkSchedule;
    public function update(int $id, array $data): WorkSchedule;
    public function delete(int $id): bool;
    public function getCurrentScheduleForEmployee(int $employeeId): ?WorkSchedule;
    public function getSchedulesByEmployee(int $employeeId): array;
}
