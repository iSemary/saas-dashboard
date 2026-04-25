<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Department;

interface DepartmentRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Department;
    public function find(int $id): ?Department;
    public function create(array $data): Department;
    public function update(int $id, array $data): Department;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function getTree(): array;
    public function getByParent(?int $parentId): array;
}
