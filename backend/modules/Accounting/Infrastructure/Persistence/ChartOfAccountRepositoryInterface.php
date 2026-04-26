<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\ChartOfAccount;
use Illuminate\Pagination\LengthAwarePaginator;

interface ChartOfAccountRepositoryInterface
{
    public function find(int $id): ?ChartOfAccount;
    public function findOrFail(int $id): ChartOfAccount;
    public function create(array $data): ChartOfAccount;
    public function update(int $id, array $data): ChartOfAccount;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function count(array $filters = []): int;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
    public function getActiveGroupedByType(): \Illuminate\Support\Collection;
}
