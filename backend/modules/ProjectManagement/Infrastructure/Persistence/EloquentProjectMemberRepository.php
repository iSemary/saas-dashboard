<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\ProjectMember;
use Illuminate\Database\Eloquent\Collection;

class EloquentProjectMemberRepository implements ProjectMemberRepositoryInterface
{
    public function find(string $id): ?ProjectMember
    {
        return ProjectMember::find($id);
    }

    public function findOrFail(string $id): ProjectMember
    {
        return ProjectMember::findOrFail($id);
    }

    public function create(array $data): ProjectMember
    {
        return ProjectMember::create($data);
    }

    public function update(string $id, array $data): ProjectMember
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
        return ProjectMember::where('project_id', $projectId)->with('user')->get();
    }
}
