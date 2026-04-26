<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\BoardColumn;
use Illuminate\Database\Eloquent\Collection;

class EloquentBoardColumnRepository implements BoardColumnRepositoryInterface
{
    public function find(string $id): ?BoardColumn
    {
        return BoardColumn::find($id);
    }

    public function findOrFail(string $id): BoardColumn
    {
        return BoardColumn::findOrFail($id);
    }

    public function create(array $data): BoardColumn
    {
        return BoardColumn::create($data);
    }

    public function update(string $id, array $data): BoardColumn
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
        return BoardColumn::where('project_id', $projectId)->orderBy('position')->get();
    }

    public function reorder(array $columns): void
    {
        foreach ($columns as $i => $col) {
            BoardColumn::where('id', $col['id'])->update(['position' => $i]);
        }
    }
}
