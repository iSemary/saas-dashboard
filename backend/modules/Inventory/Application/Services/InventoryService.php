<?php

namespace Modules\Inventory\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Domain\Contracts\StockMoveRepositoryInterface;
use Modules\Inventory\Domain\Contracts\WarehouseRepositoryInterface;
use Modules\Inventory\Models\StockMove;
use Modules\Inventory\Models\Warehouse;

class InventoryService
{
    public function __construct(
        private readonly WarehouseRepositoryInterface $warehouseRepository,
        private readonly StockMoveRepositoryInterface $stockMoveRepository,
    ) {}

    // ─── Warehouses ───────────────────────────────────────────────

    public function listWarehouses(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->warehouseRepository->paginate($filters, $perPage);
    }

    public function allWarehouses(): Collection
    {
        return $this->warehouseRepository->all();
    }

    public function findWarehouse(int $id): Warehouse
    {
        return $this->warehouseRepository->findOrFail($id);
    }

    public function createWarehouse(array $data, int $userId): Warehouse
    {
        $data['created_by'] = $userId;

        if (!empty($data['is_default'])) {
            Warehouse::where('is_default', true)->update(['is_default' => false]);
        }

        return $this->warehouseRepository->create($data);
    }

    public function updateWarehouse(int $id, array $data): Warehouse
    {
        if (!empty($data['is_default'])) {
            Warehouse::where('is_default', true)->where('id', '!=', $id)->update(['is_default' => false]);
        }
        return $this->warehouseRepository->update($id, $data);
    }

    public function deleteWarehouse(int $id): bool
    {
        $warehouse = $this->warehouseRepository->findOrFail($id);
        if ($warehouse->is_default) {
            throw new \DomainException('Cannot delete the default warehouse.');
        }
        return $this->warehouseRepository->delete($id);
    }

    // ─── Stock Moves ──────────────────────────────────────────────

    public function listStockMoves(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->stockMoveRepository->paginate($filters, $perPage);
    }

    public function findStockMove(int $id): StockMove
    {
        return $this->stockMoveRepository->findOrFail($id);
    }

    public function createStockMove(array $data, int $userId): StockMove
    {
        $data['created_by'] = $userId;
        $data['state'] = $data['state'] ?? 'draft';
        return $this->stockMoveRepository->create($data);
    }

    public function confirmStockMove(int $id): StockMove
    {
        $move = $this->stockMoveRepository->findOrFail($id);
        if ($move->isDone()) {
            throw new \DomainException('Stock move is already done.');
        }
        $move->confirm();
        return $move->fresh();
    }

    public function completeStockMove(int $id): StockMove
    {
        $move = $this->stockMoveRepository->findOrFail($id);
        if ($move->isDone()) {
            throw new \DomainException('Stock move is already done.');
        }
        $move->markAsDone();
        return $move->fresh();
    }

    public function cancelStockMove(int $id): StockMove
    {
        $move = $this->stockMoveRepository->findOrFail($id);
        if ($move->isDone()) {
            throw new \DomainException('Cannot cancel a completed stock move.');
        }
        $move->cancel();
        return $move->fresh();
    }

    public function deleteStockMove(int $id): bool
    {
        return $this->stockMoveRepository->delete($id);
    }

    public function getStockSummary(int $warehouseId, ?int $productId = null): array
    {
        return $this->stockMoveRepository->getStockSummary($warehouseId, $productId);
    }
}
