<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Label;
use Illuminate\Database\Eloquent\Collection;

interface LabelRepositoryInterface
{
    public function find(string $id): ?Label;
    public function findOrFail(string $id): Label;
    public function create(array $data): Label;
    public function update(string $id, array $data): Label;
    public function delete(string $id): bool;
    public function getByProject(string $projectId): Collection;
}
