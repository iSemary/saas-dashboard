<?php

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\SmsMarketing\Domain\Entities\SmWebhook;

interface SmWebhookRepositoryInterface
{
    public function find(int $id): ?SmWebhook;
    public function findOrFail(int $id): SmWebhook;
    public function create(array $data): SmWebhook;
    public function update(int $id, array $data): SmWebhook;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
