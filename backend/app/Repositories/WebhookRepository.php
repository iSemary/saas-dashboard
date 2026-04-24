<?php

namespace App\Repositories;

use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WebhookRepository implements WebhookRepositoryInterface
{
    public function allByUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return Webhook::with('creator')
            ->where('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findWithRelations(int $id): ?Webhook
    {
        return Webhook::with(['creator', 'logs' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(50);
        }])->find($id);
    }

    public function create(array $data): Webhook
    {
        return Webhook::create($data);
    }

    public function update(int $id, array $data): Webhook
    {
        $webhook = Webhook::findOrFail($id);
        $webhook->update($data);
        return $webhook;
    }

    public function delete(int $id): bool
    {
        return Webhook::findOrFail($id)->delete();
    }

    public function findOrFail(int $id): Webhook
    {
        return Webhook::findOrFail($id);
    }

    public function paginateLogs(int $webhookId, int $perPage = 20): LengthAwarePaginator
    {
        return WebhookLog::where('webhook_id', $webhookId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function createLog(array $data): WebhookLog
    {
        return WebhookLog::create($data);
    }
}
