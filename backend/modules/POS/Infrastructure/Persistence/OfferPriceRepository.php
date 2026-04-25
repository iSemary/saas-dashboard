<?php

namespace Modules\POS\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\POS\Domain\Contracts\OfferPriceRepositoryInterface;
use Modules\POS\Domain\Entities\OfferPrice;

class OfferPriceRepository implements OfferPriceRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = OfferPrice::with(['product', 'creator']);

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }
        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): OfferPrice
    {
        return OfferPrice::with(['product', 'creator'])->findOrFail($id);
    }

    public function create(array $data): OfferPrice
    {
        return OfferPrice::create($data);
    }

    public function update(int $id, array $data): OfferPrice
    {
        $record = OfferPrice::findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) OfferPrice::findOrFail($id)->delete();
    }
}
