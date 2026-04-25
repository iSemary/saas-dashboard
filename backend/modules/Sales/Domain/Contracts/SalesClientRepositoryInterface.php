<?php

namespace Modules\Sales\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Sales\Domain\Entities\SalesClient;

interface SalesClientRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): SalesClient;
    public function findByUserId(int $userId): ?SalesClient;
    public function create(array $data): SalesClient;
    public function update(int $id, array $data): SalesClient;
    public function delete(int $id): bool;
}
