<?php

namespace Modules\POS\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\POS\Domain\Contracts\CategoryRepositoryInterface;
use Modules\POS\Domain\Entities\Category;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function all(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    public function findOrFail(int $id): Category
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, int $userId): Category
    {
        return $this->repository->create(array_merge($data, ['created_by' => $userId]));
    }

    public function update(int $id, array $data): Category
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $category = $this->repository->findOrFail($id);

        if ($category->hasProducts()) {
            throw new \DomainException('Cannot delete category with associated products.');
        }

        return $this->repository->delete($id);
    }

    public function bulkDelete(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }
}
