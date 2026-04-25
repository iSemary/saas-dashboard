<?php

namespace Modules\POS\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\POS\Domain\Entities\Damaged;

interface DamagedRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Damaged;
    public function create(array $data): Damaged;
    public function delete(int $id): bool;
}
