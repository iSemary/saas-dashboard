<?php

namespace Modules\POS\Infrastructure\Persistence;

use Modules\POS\Domain\Contracts\ProductStockRepositoryInterface;
use Modules\POS\Domain\Entities\ProductStock;

class ProductStockRepository implements ProductStockRepositoryInterface
{
    public function createStock(array $data): bool
    {
        return (bool) ProductStock::create($data);
    }

    public function updateExistingStock(int $objectId, string $model, float $amount, float $totalPrice): bool
    {
        return (bool) ProductStock::where('object_id', $objectId)
            ->where('model', $model)
            ->update(['quantity' => $amount, 'total_price' => $totalPrice]);
    }

    public function getAvailableStock(int $productId, ?int $branchId = null): int
    {
        $query = ProductStock::where('product_id', $productId);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        return (int) $query->sum('quantity');
    }

    public function deleteByObject(int $objectId, string $model): bool
    {
        return (bool) ProductStock::where('object_id', $objectId)
            ->where('model', $model)
            ->delete();
    }
}
