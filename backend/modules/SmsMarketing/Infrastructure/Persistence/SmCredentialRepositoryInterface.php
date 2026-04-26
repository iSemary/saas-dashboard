<?php

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\SmsMarketing\Domain\Entities\SmCredential;

interface SmCredentialRepositoryInterface
{
    public function find(int $id): ?SmCredential;
    public function findOrFail(int $id): SmCredential;
    public function create(array $data): SmCredential;
    public function update(int $id, array $data): SmCredential;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
