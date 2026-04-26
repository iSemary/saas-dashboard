<?php

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\EmailMarketing\Domain\Entities\EmWebhook;

interface EmWebhookRepositoryInterface
{
    public function find(int $id): ?EmWebhook;
    public function findOrFail(int $id): EmWebhook;
    public function create(array $data): EmWebhook;
    public function update(int $id, array $data): EmWebhook;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
