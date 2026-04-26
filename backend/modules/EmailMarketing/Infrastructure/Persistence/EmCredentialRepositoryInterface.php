<?php

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\EmailMarketing\Domain\Entities\EmCredential;

interface EmCredentialRepositoryInterface
{
    public function find(int $id): ?EmCredential;
    public function findOrFail(int $id): EmCredential;
    public function create(array $data): EmCredential;
    public function update(int $id, array $data): EmCredential;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
