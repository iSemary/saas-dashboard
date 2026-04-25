<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\AssetCategory;

interface AssetCategoryRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): AssetCategory;
    public function create(array $data): AssetCategory;
    public function update(int $id, array $data): AssetCategory;
    public function delete(int $id): bool;
}
