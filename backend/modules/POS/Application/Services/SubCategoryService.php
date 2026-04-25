<?php

namespace Modules\POS\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\POS\Domain\Contracts\SubCategoryRepositoryInterface;
use Modules\POS\Domain\Entities\SubCategory;

class SubCategoryService
{
    public function __construct(
        private readonly SubCategoryRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function all(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    public function findOrFail(int $id): SubCategory
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, int $userId): SubCategory
    {
        return $this->repository->create(array_merge($data, ['created_by' => $userId]));
    }

    public function update(int $id, array $data): SubCategory
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $subCategory = $this->repository->findOrFail($id);

        if ($subCategory->hasProducts()) {
            throw new \DomainException('Cannot delete sub-category with associated products.');
        }

        return $this->repository->delete($id);
    }

    public function bulkDelete(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }
}
