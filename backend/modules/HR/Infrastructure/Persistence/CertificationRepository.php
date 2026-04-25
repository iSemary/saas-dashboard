<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Certification;

class CertificationRepository implements CertificationRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Certification::query();
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Certification
    {
        return Certification::findOrFail($id);
    }

    public function create(array $data): Certification
    {
        return Certification::create($data);
    }

    public function update(int $id, array $data): Certification
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        return Certification::destroy($id) > 0;
    }
}
