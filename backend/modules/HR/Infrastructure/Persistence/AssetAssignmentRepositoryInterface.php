<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\AssetAssignment;

interface AssetAssignmentRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): AssetAssignment;
    public function create(array $data): AssetAssignment;
    public function update(int $id, array $data): AssetAssignment;
    public function delete(int $id): bool;
}
