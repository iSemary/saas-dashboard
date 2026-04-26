<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Application;

interface ApplicationRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Application;
    public function findByCandidateAndJobOpening(int $candidateId, int $jobOpeningId): ?Application;
    public function create(array $data): Application;
    public function update(int $id, array $data): Application;
    public function delete(int $id): bool;
    public function getByJobOpening(int $jobOpeningId): array;
    public function getByStage(int $stageId): array;
    public function getCountByStatus(): \Illuminate\Support\Collection;
}
