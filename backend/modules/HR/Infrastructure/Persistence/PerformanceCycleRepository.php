<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\PerformanceCycle;

class PerformanceCycleRepository implements PerformanceCycleRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = PerformanceCycle::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('start_date', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): PerformanceCycle
    {
        return PerformanceCycle::findOrFail($id);
    }

    public function create(array $data): PerformanceCycle
    {
        return PerformanceCycle::create($data);
    }

    public function update(int $id, array $data): PerformanceCycle
    {
        $cycle = $this->findOrFail($id);
        $cycle->update($data);
        return $cycle->fresh();
    }

    public function delete(int $id): bool
    {
        return PerformanceCycle::destroy($id) > 0;
    }

    public function getActive(): array
    {
        return PerformanceCycle::active()->get()->toArray();
    }

    public function getOpen(): array
    {
        return PerformanceCycle::open()->get()->toArray();
    }
}
