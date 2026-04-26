<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\BoardColumn;
use Illuminate\Database\Eloquent\Collection;

interface BoardColumnRepositoryInterface
{
    public function find(string $id): ?BoardColumn;
    public function findOrFail(string $id): BoardColumn;
    public function create(array $data): BoardColumn;
    public function update(string $id, array $data): BoardColumn;
    public function delete(string $id): bool;
    public function getByProject(string $projectId): Collection;
    public function reorder(array $columns): void;
}
