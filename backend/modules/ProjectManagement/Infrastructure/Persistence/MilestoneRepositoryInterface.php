<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Milestone;
use Illuminate\Pagination\LengthAwarePaginator;

interface MilestoneRepositoryInterface
{
    public function find(string $id): ?Milestone;
    public function findOrFail(string $id): Milestone;
    public function create(array $data): Milestone;
    public function update(string $id, array $data): Milestone;
    public function delete(string $id): bool;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
}
