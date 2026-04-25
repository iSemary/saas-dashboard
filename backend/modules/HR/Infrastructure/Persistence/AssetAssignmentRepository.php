<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\AssetAssignment;

class AssetAssignmentRepository implements AssetAssignmentRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = AssetAssignment::query();
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): AssetAssignment
    {
        return AssetAssignment::findOrFail($id);
    }

    public function create(array $data): AssetAssignment
    {
        return AssetAssignment::create($data);
    }

    public function update(int $id, array $data): AssetAssignment
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        return AssetAssignment::destroy($id) > 0;
    }
}
