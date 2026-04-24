<?php

namespace App\Services\CrossDb;

use App\Repositories\CrossDb\LandlordRepositoryInterface;

class LandlordService
{
    public function __construct(protected LandlordRepositoryInterface $repository) {}

    public function getModules(array $filters = [])
    {
        return $this->repository->getModules($filters);
    }

    public function getModule(int $id)
    {
        return $this->repository->findModule($id);
    }

    public function getModulesByIds(array $ids)
    {
        return $this->repository->getModulesByIds($ids);
    }

    public function getModuleStats(): array
    {
        return $this->repository->getModuleStats();
    }
}
