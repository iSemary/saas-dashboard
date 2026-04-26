<?php

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\SmsMarketing\Domain\Entities\SmImportJob;

interface SmImportJobRepositoryInterface
{
    public function find(int $id): ?SmImportJob;
    public function findOrFail(int $id): SmImportJob;
    public function create(array $data): SmImportJob;
    public function update(int $id, array $data): SmImportJob;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
