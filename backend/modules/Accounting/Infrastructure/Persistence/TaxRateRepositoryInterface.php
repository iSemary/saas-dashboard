<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\TaxRate;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaxRateRepositoryInterface
{
    public function find(int $id): ?TaxRate;
    public function findOrFail(int $id): TaxRate;
    public function create(array $data): TaxRate;
    public function update(int $id, array $data): TaxRate;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function count(array $filters = []): int;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
}
