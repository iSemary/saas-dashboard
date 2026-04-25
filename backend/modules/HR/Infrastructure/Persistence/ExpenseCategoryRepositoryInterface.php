<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\ExpenseCategory;

interface ExpenseCategoryRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): ExpenseCategory;
    public function create(array $data): ExpenseCategory;
    public function update(int $id, array $data): ExpenseCategory;
    public function delete(int $id): bool;
}
