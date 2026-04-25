<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\ExpenseClaim;

interface ExpenseClaimRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): ExpenseClaim;
    public function create(array $data): ExpenseClaim;
    public function update(int $id, array $data): ExpenseClaim;
    public function delete(int $id): bool;
}
