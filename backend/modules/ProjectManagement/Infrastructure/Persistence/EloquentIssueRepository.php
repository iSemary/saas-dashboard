<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Issue;
use Illuminate\Database\Eloquent\Collection;

class EloquentIssueRepository implements IssueRepositoryInterface
{
    public function find(string $id): ?Issue
    {
        return Issue::find($id);
    }

    public function findOrFail(string $id): Issue
    {
        return Issue::findOrFail($id);
    }

    public function create(array $data): Issue
    {
        return Issue::create($data);
    }

    public function update(string $id, array $data): Issue
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
        return Issue::where('project_id', $projectId)->orderBy('created_at', 'desc')->get();
    }
}
