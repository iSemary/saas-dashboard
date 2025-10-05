<?php

namespace Modules\Customer\Repository;

use Modules\Customer\Entities\BrandModuleSubscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BrandModuleSubscriptionRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getById(int $id): ?BrandModuleSubscription;
    public function getByBrandAndModule(int $brandId, string $moduleKey): ?BrandModuleSubscription;
    public function create(array $data): BrandModuleSubscription;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function getByBrand(int $brandId): Collection;
    public function getByModuleKey(string $moduleKey): Collection;
    public function getActiveSubscriptions(int $brandId): Collection;
    public function toggleSubscriptionStatus(int $id): bool;
    public function hasActiveSubscription(int $brandId, string $moduleKey): bool;
    public function getDashboardStats(): array;
    public function datatables();
}
