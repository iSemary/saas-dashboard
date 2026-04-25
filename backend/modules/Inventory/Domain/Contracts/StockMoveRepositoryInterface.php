<?php

namespace Modules\Inventory\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Inventory\Models\StockMove;

interface StockMoveRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): StockMove;
    public function create(array $data): StockMove;
    public function update(int $id, array $data): StockMove;
    public function delete(int $id): bool;
    public function getStockSummary(int $warehouseId, ?int $productId): array;
}
