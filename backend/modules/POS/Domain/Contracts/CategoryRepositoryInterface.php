<?php

namespace Modules\POS\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\POS\Domain\Entities\Category;

interface CategoryRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function all(array $filters = []): \Illuminate\Database\Eloquent\Collection;
    public function findOrFail(int $id): Category;
    public function create(array $data): Category;
    public function update(int $id, array $data): Category;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
}
