<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\Opportunity;

interface OpportunityRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Opportunity;
    public function find(int $id): ?Opportunity;
    public function create(array $data): Opportunity;
    public function update(int $id, array $data): Opportunity;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function getByStage(string $stage): Collection;
    public function getAssignedTo(int $userId): Collection;
    public function getPipelineData(): array;
    public function getStatistics(): array;
    public function countByMonth(int $year, int $month): int;
    public function countClosedWonByMonth(int $year, int $month): int;
}
