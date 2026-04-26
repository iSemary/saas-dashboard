<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Webhook;
use Illuminate\Database\Eloquent\Collection;

class EloquentWebhookRepository implements WebhookRepositoryInterface
{
    public function find(string $id): ?Webhook
    {
        return Webhook::find($id);
    }

    public function findOrFail(string $id): Webhook
    {
        return Webhook::findOrFail($id);
    }

    public function create(array $data): Webhook
    {
        return Webhook::create($data);
    }

    public function update(string $id, array $data): Webhook
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
        return Webhook::where('project_id', $projectId)->get();
    }

    public function toggle(string $id): Webhook
    {
        $item = $this->findOrFail($id);
        $item->toggle();
        return $item->fresh();
    }

    public function regenerateSecret(string $id): string
    {
        $item = $this->findOrFail($id);
        return $item->regenerateSecret();
    }
}
