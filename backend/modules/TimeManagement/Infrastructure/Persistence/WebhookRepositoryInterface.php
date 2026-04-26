<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\Webhook;
use Illuminate\Database\Eloquent\Collection;

interface WebhookRepositoryInterface
{
    public function find(string $id): ?Webhook;
    public function findOrFail(string $id): Webhook;
    public function create(array $data): Webhook;
    public function update(string $id, array $data): Webhook;
    public function delete(string $id): bool;
    public function getByTenant(string $tenantId): Collection;
    public function toggle(string $id): Webhook;
    public function regenerateSecret(string $id): string;
}
