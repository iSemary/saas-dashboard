<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\OnboardingProcess;

class OnboardingProcessRepository implements OnboardingProcessRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = OnboardingProcess::query()->with('template');
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): OnboardingProcess
    {
        return OnboardingProcess::with('template')->findOrFail($id);
    }

    public function create(array $data): OnboardingProcess
    {
        return OnboardingProcess::create($data);
    }

    public function update(int $id, array $data): OnboardingProcess
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        return OnboardingProcess::destroy($id) > 0;
    }
}
