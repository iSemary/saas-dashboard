<?php

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\SmsMarketing\Domain\Entities\SmContact;

interface SmContactRepositoryInterface
{
    public function find(int $id): ?SmContact;
    public function findOrFail(int $id): SmContact;
    public function create(array $data): SmContact;
    public function update(int $id, array $data): SmContact;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function count(array $filters = []): int;
    public function sum(string $column): float;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
