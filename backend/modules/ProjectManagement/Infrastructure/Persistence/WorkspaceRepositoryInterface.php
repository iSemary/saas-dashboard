<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Workspace;
use Illuminate\Pagination\LengthAwarePaginator;

interface WorkspaceRepositoryInterface
{
    public function find(string $id): ?Workspace;
    public function findOrFail(string $id): Workspace;
    public function create(array $data): Workspace;
    public function update(string $id, array $data): Workspace;
    public function delete(string $id): bool;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function count(array $filters = []): int;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
}
