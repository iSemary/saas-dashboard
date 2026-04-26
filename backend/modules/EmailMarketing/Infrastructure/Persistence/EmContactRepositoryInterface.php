<?php

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\EmailMarketing\Domain\Entities\EmContact;

interface EmContactRepositoryInterface
{
    public function find(int $id): ?EmContact;
    public function findOrFail(int $id): EmContact;
    public function create(array $data): EmContact;
    public function update(int $id, array $data): EmContact;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function count(array $filters = []): int;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
