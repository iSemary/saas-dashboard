<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\AssetCategory;

class AssetCategoryRepository implements AssetCategoryRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return AssetCategory::query()->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): AssetCategory
    {
        return AssetCategory::findOrFail($id);
    }

    public function create(array $data): AssetCategory
    {
        return AssetCategory::create($data);
    }

    public function update(int $id, array $data): AssetCategory
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        return AssetCategory::destroy($id) > 0;
    }
}
