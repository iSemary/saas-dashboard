<?php

namespace App\Services\CrossDb;

use App\Repositories\CrossDb\TenantRepositoryInterface;

class TenantService
{
    public function __construct(protected TenantRepositoryInterface $repository) {}

    public function getBrands(array $filters = [])
    {
        return $this->repository->getBrands($filters);
    }

    public function getBrand(int $id)
    {
        return $this->repository->findBrand($id);
    }

    public function getBrandModules(int $brandId)
    {
        return $this->repository->getBrandModules($brandId);
    }

    public function assignBrandModules(int $brandId, array $moduleIds): int
    {
        if (empty($moduleIds)) {
            throw new \InvalidArgumentException('No module IDs provided');
        }

        return $this->repository->assignBrandModules($brandId, $moduleIds);
    }

    public function getBrandStats(): array
    {
        return $this->repository->getBrandStats();
    }
}
