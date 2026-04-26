<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\ProjectTemplate;
use Illuminate\Database\Eloquent\Collection;

interface ProjectTemplateRepositoryInterface
{
    public function find(string $id): ?ProjectTemplate;
    public function findOrFail(string $id): ProjectTemplate;
    public function create(array $data): ProjectTemplate;
    public function update(string $id, array $data): ProjectTemplate;
    public function delete(string $id): bool;
    public function all(): Collection;
}
