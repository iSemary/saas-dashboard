<?php

namespace Modules\POS\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\POS\Domain\Contracts\ProductRepositoryInterface;
use Modules\POS\Domain\Entities\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::with(['category', 'subCategory', 'barcodes', 'tags', 'creator']);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (!empty($filters['sub_category_id'])) {
            $query->where('sub_category_id', $filters['sub_category_id']);
        }
        if (!empty($filters['is_offer'])) {
            $query->where('is_offer', true);
        }
        if (!empty($filters['expired'])) {
            $query->whereNotNull('expired_at')->where('expired_at', '<', now());
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Product
    {
        return Product::with(['category', 'subCategory', 'barcodes', 'tags', 'productStocks', 'creator'])->findOrFail($id);
    }

    public function findByBarcode(string $barcode): ?Product
    {
        return Product::whereHas('barcodes', fn($q) => $q->where('barcode_number', $barcode))->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) Product::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return Product::whereIn('id', $ids)->delete();
    }

    public function getAvailableStock(int $productId, ?int $branchId = null): int
    {
        $query = Product::findOrFail($productId)->productStocks();
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        return (int) $query->sum('quantity');
    }
}
