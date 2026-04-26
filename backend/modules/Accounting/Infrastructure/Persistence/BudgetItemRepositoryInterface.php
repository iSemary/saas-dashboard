<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\BudgetItem;
use Illuminate\Pagination\LengthAwarePaginator;

interface BudgetItemRepositoryInterface
{
    public function find(int $id): ?BudgetItem;
    public function findOrFail(int $id): BudgetItem;
    public function create(array $data): BudgetItem;
    public function update(int $id, array $data): BudgetItem;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
}
