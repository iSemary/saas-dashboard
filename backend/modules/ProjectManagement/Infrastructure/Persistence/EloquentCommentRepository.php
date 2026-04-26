<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Comment;
use Illuminate\Database\Eloquent\Collection;

class EloquentCommentRepository implements CommentRepositoryInterface
{
    public function find(string $id): ?Comment
    {
        return Comment::find($id);
    }

    public function findOrFail(string $id): Comment
    {
        return Comment::findOrFail($id);
    }

    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    public function delete(string $id): bool
    {
        $item = $this->find($id);
        return $item ? $item->delete() : false;
    }

    public function getByProject(string $projectId): Collection
    {
        return Comment::where('project_id', $projectId)->with('user')->orderBy('created_at', 'desc')->get();
    }
}
