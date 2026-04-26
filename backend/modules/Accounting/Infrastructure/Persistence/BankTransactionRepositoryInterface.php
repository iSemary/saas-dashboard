<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\BankTransaction;
use Illuminate\Pagination\LengthAwarePaginator;

interface BankTransactionRepositoryInterface
{
    public function find(int $id): ?BankTransaction;
    public function findOrFail(int $id): BankTransaction;
    public function create(array $data): BankTransaction;
    public function update(int $id, array $data): BankTransaction;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
}
