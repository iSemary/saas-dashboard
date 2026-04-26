<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\ProjectMember;
use Illuminate\Database\Eloquent\Collection;

interface ProjectMemberRepositoryInterface
{
    public function find(string $id): ?ProjectMember;
    public function findOrFail(string $id): ProjectMember;
    public function create(array $data): ProjectMember;
    public function update(string $id, array $data): ProjectMember;
    public function delete(string $id): bool;
    public function getByProject(string $projectId): Collection;
}
