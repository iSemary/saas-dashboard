<?php

namespace Modules\POS\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\POS\Domain\Contracts\BarcodeRepositoryInterface;
use Modules\POS\Domain\Entities\Barcode;

class BarcodeRepository implements BarcodeRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Barcode::with(['product', 'category', 'creator']);

        if (!empty($filters['search'])) {
            $query->where('barcode_number', 'like', "%{$filters['search']}%");
        }
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findByNumber(string $barcodeNumber): ?Barcode
    {
        return Barcode::where('barcode_number', $barcodeNumber)->with(['product', 'category'])->first();
    }

    public function findOrFail(int $id): Barcode
    {
        return Barcode::with(['product', 'category'])->findOrFail($id);
    }

    public function create(array $data): Barcode
    {
        return Barcode::create($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Barcode::findOrFail($id)->delete();
    }
}
