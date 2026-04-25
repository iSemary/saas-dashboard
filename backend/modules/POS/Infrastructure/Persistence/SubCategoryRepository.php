<?php

namespace Modules\POS\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\POS\Domain\Contracts\SubCategoryRepositoryInterface;
use Modules\POS\Domain\Entities\SubCategory;

class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SubCategory::with(['category', 'creator']);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->withCount('products')->latest()->paginate($perPage);
    }

    public function all(array $filters = []): Collection
    {
        $query = SubCategory::with('category');
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        return $query->orderBy('name')->get();
    }

    public function findOrFail(int $id): SubCategory
    {
        return SubCategory::with(['category', 'creator'])->findOrFail($id);
    }

    public function create(array $data): SubCategory
    {
        return SubCategory::create($data);
    }

    public function update(int $id, array $data): SubCategory
    {
        $sub = SubCategory::findOrFail($id);
        $sub->update($data);
        return $sub->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) SubCategory::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return SubCategory::whereIn('id', $ids)->delete();
    }
}
