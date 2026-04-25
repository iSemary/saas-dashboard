<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Asset;

class AssetRepository implements AssetRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Asset::query();
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Asset
    {
        return Asset::findOrFail($id);
    }

    public function create(array $data): Asset
    {
        return Asset::create($data);
    }

    public function update(int $id, array $data): Asset
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        return Asset::destroy($id) > 0;
    }
}
