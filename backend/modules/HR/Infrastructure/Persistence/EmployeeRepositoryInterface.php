<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Employee;

interface EmployeeRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Employee;
    public function find(int $id): ?Employee;
    public function findByUserId(int $userId): ?Employee;
    public function findByEmployeeNumber(string $employeeNumber): ?Employee;
    public function create(array $data): Employee;
    public function update(int $id, array $data): Employee;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function getSubordinates(int $managerId): array;
    public function getByDepartment(int $departmentId): array;
    public function getOrgChart(): array;
}
