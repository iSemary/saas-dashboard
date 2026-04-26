<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Task;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function find(string $id): ?Task;
    public function findOrFail(string $id): Task;
    public function create(array $data): Task;
    public function update(string $id, array $data): Task;
    public function delete(string $id): bool;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function exists(string $id): bool;
    public function count(array $filters = []): int;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
    public function getWorkloadByAssignee(?string $tenantId = null): \Illuminate\Support\Collection;
}
