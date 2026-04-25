<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\OnboardingTemplate;

class OnboardingTemplateRepository implements OnboardingTemplateRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = OnboardingTemplate::query();
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): OnboardingTemplate
    {
        return OnboardingTemplate::findOrFail($id);
    }

    public function create(array $data): OnboardingTemplate
    {
        return OnboardingTemplate::create($data);
    }

    public function update(int $id, array $data): OnboardingTemplate
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        return OnboardingTemplate::destroy($id) > 0;
    }
}
