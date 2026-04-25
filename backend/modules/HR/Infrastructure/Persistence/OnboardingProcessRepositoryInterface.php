<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\OnboardingProcess;

interface OnboardingProcessRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): OnboardingProcess;
    public function create(array $data): OnboardingProcess;
    public function update(int $id, array $data): OnboardingProcess;
    public function delete(int $id): bool;
}
