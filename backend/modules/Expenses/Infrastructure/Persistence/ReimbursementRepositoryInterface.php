<?php
declare(strict_types=1);
namespace Modules\Expenses\Infrastructure\Persistence;

use Modules\Expenses\Domain\Entities\Reimbursement;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReimbursementRepositoryInterface
{
    public function find(int $id): ?Reimbursement;
    public function findOrFail(int $id): Reimbursement;
    public function create(array $data): Reimbursement;
    public function update(int $id, array $data): Reimbursement;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function count(array $filters = []): int;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
    public function sumByCreatorAndStatus(int $userId, string $status, string $column): float;
}
