<?php

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\EmailMarketing\Domain\Entities\EmImportJob;

interface EmImportJobRepositoryInterface
{
    public function find(int $id): ?EmImportJob;
    public function findOrFail(int $id): EmImportJob;
    public function create(array $data): EmImportJob;
    public function update(int $id, array $data): EmImportJob;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
