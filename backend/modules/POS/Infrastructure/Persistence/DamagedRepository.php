<?php

namespace Modules\POS\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\POS\Domain\Contracts\DamagedRepositoryInterface;
use Modules\POS\Domain\Entities\Damaged;

class DamagedRepository implements DamagedRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Damaged::with(['product', 'creator']);

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }
        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Damaged
    {
        return Damaged::with(['product', 'creator'])->findOrFail($id);
    }

    public function create(array $data): Damaged
    {
        return Damaged::create($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Damaged::findOrFail($id)->delete();
    }
}
