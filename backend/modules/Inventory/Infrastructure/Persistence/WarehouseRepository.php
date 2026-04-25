<?php

namespace Modules\Inventory\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Inventory\Domain\Contracts\WarehouseRepositoryInterface;
use Modules\Inventory\Models\Warehouse;

class WarehouseRepository implements WarehouseRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Warehouse::with(['manager', 'creator']);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('code', 'like', "%{$filters['search']}%");
        }
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->withCount('stockMoves')->latest()->paginate($perPage);
    }

    public function all(): Collection
    {
        return Warehouse::active()->orderBy('name')->get();
    }

    public function findOrFail(int $id): Warehouse
    {
        return Warehouse::with(['manager', 'creator', 'stockMoves', 'reorderRules'])->findOrFail($id);
    }

    public function create(array $data): Warehouse
    {
        return Warehouse::create($data);
    }

    public function update(int $id, array $data): Warehouse
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update($data);
        return $warehouse->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) Warehouse::findOrFail($id)->delete();
    }

    public function getDefault(): ?Warehouse
    {
        return Warehouse::getDefault();
    }
}
