<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\BoardSwimlane;
use Illuminate\Database\Eloquent\Collection;

class EloquentBoardSwimlaneRepository implements BoardSwimlaneRepositoryInterface
{
    public function find(string $id): ?BoardSwimlane
    {
        return BoardSwimlane::find($id);
    }

    public function findOrFail(string $id): BoardSwimlane
    {
        return BoardSwimlane::findOrFail($id);
    }

    public function create(array $data): BoardSwimlane
    {
        return BoardSwimlane::create($data);
    }

    public function update(string $id, array $data): BoardSwimlane
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
        return BoardSwimlane::where('project_id', $projectId)->orderBy('position')->get();
    }

    public function reorder(array $swimlanes): void
    {
        foreach ($swimlanes as $i => $swim) {
            BoardSwimlane::where('id', $swim['id'])->update(['position' => $i]);
        }
    }
}
