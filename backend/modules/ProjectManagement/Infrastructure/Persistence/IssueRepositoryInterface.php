<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Issue;
use Illuminate\Database\Eloquent\Collection;

interface IssueRepositoryInterface
{
    public function find(string $id): ?Issue;
    public function findOrFail(string $id): Issue;
    public function create(array $data): Issue;
    public function update(string $id, array $data): Issue;
    public function delete(string $id): bool;
    public function getByProject(string $projectId): Collection;
}
