<?php

namespace Modules\POS\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\POS\Domain\Entities\Product;

interface ProductRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Product;
    public function findByBarcode(string $barcode): ?Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): Product;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function getAvailableStock(int $productId, ?int $branchId = null): int;
}
