<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\JournalEntry;
use Illuminate\Pagination\LengthAwarePaginator;

interface JournalEntryRepositoryInterface
{
    public function find(int $id): ?JournalEntry;
    public function findOrFail(int $id): JournalEntry;
    public function create(array $data): JournalEntry;
    public function update(int $id, array $data): JournalEntry;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function count(array $filters = []): int;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
    public function sumPosted(string $column): float;
}
