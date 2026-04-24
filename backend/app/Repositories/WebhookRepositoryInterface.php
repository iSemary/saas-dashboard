<?php

namespace App\Repositories;

use App\Models\Webhook;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface WebhookRepositoryInterface
{
    public function allByUser(int $userId): \Illuminate\Database\Eloquent\Collection;

    public function findWithRelations(int $id): ?Webhook;

    public function create(array $data): Webhook;

    public function update(int $id, array $data): Webhook;

    public function delete(int $id): bool;

    public function findOrFail(int $id): Webhook;

    public function paginateLogs(int $webhookId, int $perPage = 20): LengthAwarePaginator;

    public function createLog(array $data): \App\Models\WebhookLog;
}
