<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Policy;

interface PolicyRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Policy;
    public function create(array $data): Policy;
    public function update(int $id, array $data): Policy;
    public function delete(int $id): bool;
}
