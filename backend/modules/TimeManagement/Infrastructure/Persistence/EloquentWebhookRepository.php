<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\Webhook;
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

    public function getByTenant(string $tenantId): Collection
    {
        return Webhook::where('tenant_id', $tenantId)->get();
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
