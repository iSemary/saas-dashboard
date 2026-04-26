<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\ProjectTemplate;
use Illuminate\Database\Eloquent\Collection;

class EloquentProjectTemplateRepository implements ProjectTemplateRepositoryInterface
{
    public function find(string $id): ?ProjectTemplate
    {
        return ProjectTemplate::find($id);
    }

    public function findOrFail(string $id): ProjectTemplate
    {
        return ProjectTemplate::findOrFail($id);
    }

    public function create(array $data): ProjectTemplate
    {
        return ProjectTemplate::create($data);
    }

    public function update(string $id, array $data): ProjectTemplate
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

    public function all(): Collection
    {
        return ProjectTemplate::orderBy('created_at', 'desc')->get();
    }
}
