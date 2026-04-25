<?php

namespace Modules\Sales\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Sales\Domain\Entities\SalesOrder;

interface SalesOrderRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): SalesOrder;
    public function findByBarcode(string $barcode): ?SalesOrder;
    public function create(array $data): SalesOrder;
    public function update(int $id, array $data): SalesOrder;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function getDailySummary(?string $date, ?int $branchId): array;
}
