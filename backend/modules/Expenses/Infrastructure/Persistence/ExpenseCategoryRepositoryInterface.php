<?php
declare(strict_types=1);
namespace Modules\Expenses\Infrastructure\Persistence;

use Modules\Expenses\Domain\Entities\ExpenseCategory;
use Illuminate\Pagination\LengthAwarePaginator;

interface ExpenseCategoryRepositoryInterface
{
    public function find(int $id): ?ExpenseCategory;
    public function findOrFail(int $id): ExpenseCategory;
    public function create(array $data): ExpenseCategory;
    public function update(int $id, array $data): ExpenseCategory;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function count(array $filters = []): int;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
}
