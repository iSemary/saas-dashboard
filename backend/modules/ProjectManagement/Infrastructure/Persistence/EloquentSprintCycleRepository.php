<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\SprintCycle;
use Illuminate\Database\Eloquent\Collection;

class EloquentSprintCycleRepository implements SprintCycleRepositoryInterface
{
    public function find(string $id): ?SprintCycle
    {
        return SprintCycle::find($id);
    }

    public function findOrFail(string $id): SprintCycle
    {
        return SprintCycle::findOrFail($id);
    }

    public function create(array $data): SprintCycle
    {
        return SprintCycle::create($data);
    }

    public function update(string $id, array $data): SprintCycle
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(string $id): bool
    {
        $item = $this->find($id);
        return $item ? $item->delete() : false;
    }

    public function getByProject(string $projectId): Collection
    {
        return SprintCycle::where('project_id', $projectId)->orderBy('created_at', 'desc')->get();
    }
}
