<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\JobOpening;

class JobOpeningRepository implements JobOpeningRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = JobOpening::query()->with(['department', 'position', 'hiringManager', 'recruiter']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%')
                ->orWhere('description', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): JobOpening
    {
        return JobOpening::with(['department', 'position', 'applications'])->findOrFail($id);
    }

    public function create(array $data): JobOpening
    {
        return JobOpening::create($data);
    }

    public function update(int $id, array $data): JobOpening
    {
        $opening = $this->findOrFail($id);
        $opening->update($data);
        return $opening->fresh();
    }

    public function delete(int $id): bool
    {
        return JobOpening::destroy($id) > 0;
    }

    public function getPublished(): array
    {
        return JobOpening::published()
            ->with(['department'])
            ->get()
            ->toArray();
    }

    public function getActive(): array
    {
        return JobOpening::active()
            ->with(['department'])
            ->get()
            ->toArray();
    }
}
