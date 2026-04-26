<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Label;
use Illuminate\Database\Eloquent\Collection;

class EloquentLabelRepository implements LabelRepositoryInterface
{
    public function find(string $id): ?Label
    {
        return Label::find($id);
    }

    public function findOrFail(string $id): Label
    {
        return Label::findOrFail($id);
    }

    public function create(array $data): Label
    {
        return Label::create($data);
    }

    public function update(string $id, array $data): Label
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
        return Label::where('project_id', $projectId)->get();
    }
}
