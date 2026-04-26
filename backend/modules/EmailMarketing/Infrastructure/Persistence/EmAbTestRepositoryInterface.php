<?php

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\EmailMarketing\Domain\Entities\EmAbTest;

interface EmAbTestRepositoryInterface
{
    public function find(int $id): ?EmAbTest;
    public function findOrFail(int $id): EmAbTest;
    public function create(array $data): EmAbTest;
    public function update(int $id, array $data): EmAbTest;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
