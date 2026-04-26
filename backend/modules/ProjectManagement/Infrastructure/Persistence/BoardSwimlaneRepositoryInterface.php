<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\BoardSwimlane;
use Illuminate\Database\Eloquent\Collection;

interface BoardSwimlaneRepositoryInterface
{
    public function find(string $id): ?BoardSwimlane;
    public function findOrFail(string $id): BoardSwimlane;
    public function create(array $data): BoardSwimlane;
    public function update(string $id, array $data): BoardSwimlane;
    public function delete(string $id): bool;
    public function getByProject(string $projectId): Collection;
    public function reorder(array $swimlanes): void;
}
