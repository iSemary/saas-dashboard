<?php

namespace Modules\POS\Domain\Contracts;

interface ProductStockRepositoryInterface
{
    public function createStock(array $data): bool;
    public function updateExistingStock(int $objectId, string $model, float $amount, float $totalPrice): bool;
    public function getAvailableStock(int $productId, ?int $branchId = null): int;
    public function deleteByObject(int $objectId, string $model): bool;
}
