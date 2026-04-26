<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\JournalItem;
use Illuminate\Pagination\LengthAwarePaginator;

interface JournalItemRepositoryInterface
{
    public function find(int $id): ?JournalItem;
    public function findOrFail(int $id): JournalItem;
    public function create(array $data): JournalItem;
    public function update(int $id, array $data): JournalItem;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
}
