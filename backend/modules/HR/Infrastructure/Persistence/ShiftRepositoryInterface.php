<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Shift;

interface ShiftRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Shift;
    public function create(array $data): Shift;
    public function update(int $id, array $data): Shift;
    public function delete(int $id): bool;
    public function getActiveShifts(): array;
}
