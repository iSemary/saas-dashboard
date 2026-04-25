<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Goal;

interface GoalRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Goal;
    public function create(array $data): Goal;
    public function update(int $id, array $data): Goal;
    public function delete(int $id): bool;
    public function getByEmployee(int $employeeId): array;
    public function getActiveByEmployee(int $employeeId): array;
}
