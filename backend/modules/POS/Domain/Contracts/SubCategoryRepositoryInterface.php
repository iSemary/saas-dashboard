<?php

namespace Modules\POS\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\POS\Domain\Entities\SubCategory;

interface SubCategoryRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function all(array $filters = []): \Illuminate\Database\Eloquent\Collection;
    public function findOrFail(int $id): SubCategory;
    public function create(array $data): SubCategory;
    public function update(int $id, array $data): SubCategory;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
}
