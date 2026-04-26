<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Comment;
use Illuminate\Database\Eloquent\Collection;

interface CommentRepositoryInterface
{
    public function find(string $id): ?Comment;
    public function findOrFail(string $id): Comment;
    public function create(array $data): Comment;
    public function delete(string $id): bool;
    public function getByProject(string $projectId): Collection;
}
