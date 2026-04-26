<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Risk;
use Illuminate\Database\Eloquent\Collection;

interface RiskRepositoryInterface
{
    public function find(string $id): ?Risk;
    public function findOrFail(string $id): Risk;
    public function create(array $data): Risk;
    public function update(string $id, array $data): Risk;
    public function delete(string $id): bool;
    public function getByProject(string $projectId): Collection;
}
