<?php

namespace Modules\Inventory\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Inventory\Models\Warehouse;

interface WarehouseRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function all(): Collection;
    public function findOrFail(int $id): Warehouse;
    public function create(array $data): Warehouse;
    public function update(int $id, array $data): Warehouse;
    public function delete(int $id): bool;
    public function getDefault(): ?Warehouse;
}
