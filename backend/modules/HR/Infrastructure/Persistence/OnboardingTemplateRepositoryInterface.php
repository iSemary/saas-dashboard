<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\OnboardingTemplate;

interface OnboardingTemplateRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): OnboardingTemplate;
    public function create(array $data): OnboardingTemplate;
    public function update(int $id, array $data): OnboardingTemplate;
    public function delete(int $id): bool;
}
