<?php

namespace Modules\Customer\Repository;

use Modules\Customer\Entities\Branch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BranchRepositoryInterface
{
    public function getAll(array $params = []): LengthAwarePaginator|Collection;
    public function getAllLegacy(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getById(int $id): ?Branch;
    public function getByCode(string $code): ?Branch;
    public function create(array $data): Branch;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function getByBrand(int $brandId): Collection;
    public function search(string $query): Collection;
    public function getDashboardStats(): array;
    public function datatables();
    public function getActiveBranches(): Collection;
    public function getBranchesByLocation(string $city = null, string $state = null, string $country = null): Collection;
}
