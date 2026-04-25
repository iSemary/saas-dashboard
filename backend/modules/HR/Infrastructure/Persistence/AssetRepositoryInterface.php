<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Asset;

interface AssetRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Asset;
    public function create(array $data): Asset;
    public function update(int $id, array $data): Asset;
    public function delete(int $id): bool;
}
