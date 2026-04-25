<?php

namespace Modules\Customer\Repository;

use Modules\Customer\Entities\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BrandRepositoryInterface
{
    public function getAll(array $params = []): LengthAwarePaginator|Collection;
    public function getAllLegacy(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getById(int $id): ?Brand;
    public function getBySlug(string $slug): ?Brand;
    public function create(array $data): Brand;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function getByTenant(int $tenantId): Collection;
    public function search(string $query): Collection;
    public function getDashboardStats(): array;
    public function datatables();
    public function tenantDataTables(int $tenantId);
}
