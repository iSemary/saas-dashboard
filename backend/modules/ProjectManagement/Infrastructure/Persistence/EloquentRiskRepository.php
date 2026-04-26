<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Risk;
use Illuminate\Database\Eloquent\Collection;

class EloquentRiskRepository implements RiskRepositoryInterface
{
    public function find(string $id): ?Risk
    {
        return Risk::find($id);
    }

    public function findOrFail(string $id): Risk
    {
        return Risk::findOrFail($id);
    }

    public function create(array $data): Risk
    {
        return Risk::create($data);
    }

    public function update(string $id, array $data): Risk
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
        return Risk::where('project_id', $projectId)->orderBy('created_at', 'desc')->get();
    }
}
