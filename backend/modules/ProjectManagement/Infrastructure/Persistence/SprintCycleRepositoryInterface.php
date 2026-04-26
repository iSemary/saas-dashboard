<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\SprintCycle;
use Illuminate\Database\Eloquent\Collection;

interface SprintCycleRepositoryInterface
{
    public function find(string $id): ?SprintCycle;
    public function findOrFail(string $id): SprintCycle;
    public function create(array $data): SprintCycle;
    public function update(string $id, array $data): SprintCycle;
    public function delete(string $id): bool;
    public function getByProject(string $projectId): Collection;
}
