<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Shift;

class ShiftRepository implements ShiftRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Shift::query();

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function findOrFail(int $id): Shift
    {
        return Shift::findOrFail($id);
    }

    public function create(array $data): Shift
    {
        return Shift::create($data);
    }

    public function update(int $id, array $data): Shift
    {
        $shift = $this->findOrFail($id);
        $shift->update($data);
        return $shift->fresh();
    }

    public function delete(int $id): bool
    {
        return Shift::destroy($id) > 0;
    }

    public function getActiveShifts(): array
    {
        return Shift::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}
