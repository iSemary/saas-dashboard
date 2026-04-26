<?php

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\SmsMarketing\Domain\Entities\SmAbTest;

interface SmAbTestRepositoryInterface
{
    public function find(int $id): ?SmAbTest;
    public function findOrFail(int $id): SmAbTest;
    public function create(array $data): SmAbTest;
    public function update(int $id, array $data): SmAbTest;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
