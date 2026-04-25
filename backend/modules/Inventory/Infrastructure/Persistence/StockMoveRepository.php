<?php

namespace Modules\Inventory\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Domain\Contracts\StockMoveRepositoryInterface;
use Modules\Inventory\Models\StockMove;

class StockMoveRepository implements StockMoveRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = StockMove::with(['warehouse', 'creator']);

        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }
        if (!empty($filters['move_type'])) {
            $query->where('move_type', $filters['move_type']);
        }
        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): StockMove
    {
        return StockMove::with(['warehouse', 'creator'])->findOrFail($id);
    }

    public function create(array $data): StockMove
    {
        return StockMove::create($data);
    }

    public function update(int $id, array $data): StockMove
    {
        $move = StockMove::findOrFail($id);
        $move->update($data);
        return $move->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) StockMove::findOrFail($id)->delete();
    }

    public function getStockSummary(int $warehouseId, ?int $productId): array
    {
        $query = StockMove::where('warehouse_id', $warehouseId)->where('state', 'done');
        if ($productId) {
            $query->where('product_id', $productId);
        }

        $inbound  = (clone $query)->where('move_type', 'in')->sum('quantity');
        $outbound = (clone $query)->where('move_type', 'out')->sum('quantity');

        return [
            'warehouse_id' => $warehouseId,
            'product_id'   => $productId,
            'inbound'      => (float) $inbound,
            'outbound'     => (float) $outbound,
            'balance'      => (float) ($inbound - $outbound),
        ];
    }
}
