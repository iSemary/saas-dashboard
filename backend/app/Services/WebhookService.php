<?php

namespace App\Services;

use App\Repositories\WebhookRepositoryInterface;
use App\Models\Webhook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebhookService extends BaseService
{
    public function __construct(protected WebhookRepositoryInterface $repository) {}

    public function listByUser(int $userId)
    {
        return $this->repository->allByUser($userId);
    }

    public function create(array $data): Webhook
    {
        $data['status'] = $data['status'] ?? 'active';
        $data['timeout'] = $data['timeout'] ?? 30;
        $data['retry_count'] = $data['retry_count'] ?? 3;
        $data['secret'] = $data['secret'] ?? Str::random(32);

        return $this->repository->create($data);
    }

    public function show(int $id): ?Webhook
    {
        return $this->repository->findWithRelations($id);
    }

    public function update(int $id, array $data): Webhook
    {
        return $this->repository->update($id, $data);
    }

    public function isOwner(int $webhookId, int $userId): bool
    {
        $webhook = $this->repository->findOrFail($webhookId);
        return $webhook->created_by === $userId;
    }

    public function test(int $webhookId, int $userId, array $payload = []): array
    {
        $webhook = $this->repository->findOrFail($webhookId);

        if ($webhook->created_by !== $userId) {
            throw new \Exception(translate('auth.unauthorized'));
        }

        $payload = !empty($payload) ? $payload : ['test' => true, 'timestamp' => now()->toIso8601String()];

        return $this->sendWebhook($webhook, 'test', $payload);
    }

    public function getLogs(int $webhookId, int $userId, int $perPage = 20)
    {
        return $this->repository->paginateLogs($webhookId, $perPage);
    }

    public function sendWebhook(Webhook $webhook, string $event, array $payload): array
    {
        $headers = $webhook->headers ?? [];
        $headers['Content-Type'] = 'application/json';
        $headers['User-Agent'] = 'SaaS-Dashboard-Webhook/1.0';

        if ($webhook->secret) {
            $signature = hash_hmac('sha256', json_encode($payload), $webhook->secret);
            $headers['X-Webhook-Signature'] = $signature;
        }

        try {
            $response = Http::timeout($webhook->timeout)
                ->withHeaders($headers)
                ->post($webhook->url, $payload);

            $this->repository->createLog([
                'webhook_id' => $webhook->id,
                'event' => $event,
                'payload' => $payload,
                'status_code' => $response->status(),
                'response' => $response->body(),
                'delivered_at' => now(),
            ]);

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'response' => $response->body(),
            ];
        } catch (\Exception $e) {
            $this->repository->createLog([
                'webhook_id' => $webhook->id,
                'event' => $event,
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
