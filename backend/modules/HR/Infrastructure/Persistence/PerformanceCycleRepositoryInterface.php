<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\PerformanceCycle;

interface PerformanceCycleRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): PerformanceCycle;
    public function create(array $data): PerformanceCycle;
    public function update(int $id, array $data): PerformanceCycle;
    public function delete(int $id): bool;
    public function getActive(): array;
    public function getOpen(): array;
}
