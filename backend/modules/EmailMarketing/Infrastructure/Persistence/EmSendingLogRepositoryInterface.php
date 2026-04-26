<?php

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\EmailMarketing\Domain\Entities\EmSendingLog;

interface EmSendingLogRepositoryInterface
{
    public function find(int $id): ?EmSendingLog;
    public function findOrFail(int $id): EmSendingLog;
    public function create(array $data): EmSendingLog;
    public function update(int $id, array $data): EmSendingLog;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function count(array $filters = []): int;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
