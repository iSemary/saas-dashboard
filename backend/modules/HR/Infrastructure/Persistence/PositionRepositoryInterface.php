<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Position;

interface PositionRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Position;
    public function find(int $id): ?Position;
    public function create(array $data): Position;
    public function update(int $id, array $data): Position;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function getByDepartment(int $departmentId): array;
}
