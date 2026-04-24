<?php

namespace App\Repositories\CrossDb;

interface TenantRepositoryInterface
{
    public function getBrands(array $filters = []): \Illuminate\Database\Eloquent\Collection;

    public function findBrand(int $id): ?\Modules\Customer\Entities\Tenant\Brand;

    public function getBrandModules(int $brandId): \Illuminate\Database\Eloquent\Collection;

    public function assignBrandModules(int $brandId, array $moduleIds): int;

    public function getBrandStats(): array;
}
