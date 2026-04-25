<?php

namespace Modules\POS\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\POS\Models\Product;

interface ProductRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): Product;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function searchByBarcode(string $barcode): ?Product;
    public function searchByName(string $name): LengthAwarePaginator;
    public function getStockSummary(int $productId, ?int $branchId = null): int;
}
