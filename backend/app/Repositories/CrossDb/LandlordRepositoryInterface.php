<?php

namespace App\Repositories\CrossDb;

interface LandlordRepositoryInterface
{
    public function getModules(array $filters = []): \Illuminate\Support\Collection;

    public function findModule(int $id): ?\Modules\Utilities\Entities\Module;

    public function getModulesByIds(array $ids): \Illuminate\Database\Eloquent\Collection;

    public function getModuleStats(): array;
}
