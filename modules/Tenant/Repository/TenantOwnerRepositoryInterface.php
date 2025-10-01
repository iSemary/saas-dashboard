<?php

namespace Modules\Tenant\Repository;

use Modules\Tenant\Entities\TenantOwner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TenantOwnerRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getById(int $id): ?TenantOwner;
    public function getByTenantAndUser(int $tenantId, int $userId): ?TenantOwner;
    public function create(array $data): TenantOwner;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function getTenantOwnersForTenant(int $tenantId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getSuperAdminsForTenant(int $tenantId): Collection;
    public function search(string $query): Collection;
    public function getDashboardStats(): array;
    public function getByRole(string $role): Collection;
    public function getByStatus(string $status): Collection;
    public function isUserTenantOwner(int $userId, int $tenantId): bool;
    public function isUserSuperAdmin(int $userId, int $tenantId): bool;
    public function getTenantOwnersByUser(int $userId): Collection;
}
