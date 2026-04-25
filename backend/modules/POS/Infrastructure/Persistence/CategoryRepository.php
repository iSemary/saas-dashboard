<?php

namespace Modules\POS\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\POS\Domain\Contracts\CategoryRepositoryInterface;
use Modules\POS\Domain\Entities\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Category::with(['creator']);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        return $query->withCount('products')->latest()->paginate($perPage);
    }

    public function all(array $filters = []): Collection
    {
        $query = Category::query();
        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }
        return $query->orderBy('name')->get();
    }

    public function findOrFail(int $id): Category
    {
        return Category::with(['subCategories', 'creator'])->findOrFail($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = Category::findOrFail($id);
        $category->update($data);
        return $category->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) Category::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return Category::whereIn('id', $ids)->delete();
    }
}
