<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Infrastructure\Persistence\CrmWebhookRepositoryInterface;

class CrmWebhookApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly CrmWebhookRepositoryInterface $webhooks) {}

    public function index(Request $request): JsonResponse
    {
        try {
            return $this->apiPaginated($this->webhooks->paginate([], (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve webhooks', 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'url' => 'required|url|max:500',
                'events' => 'required|array',
                'secret' => 'nullable|string|max:255',
            ]);
            $data = $request->all();
            $data['created_by'] = auth()->id();
            $data['is_active'] = $request->boolean('is_active', true);
            if (empty($data['secret'])) {
                $data['secret'] = bin2hex(random_bytes(16));
            }
            $webhook = $this->webhooks->create($data);
            return $this->apiSuccess($webhook, 'Webhook created', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create webhook', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->webhooks->findOrFail($id));
        } catch (\Throwable $e) {
            return $this->apiError('Webhook not found', 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $webhook = $this->webhooks->update($id, $request->all());
            return $this->apiSuccess($webhook, 'Webhook updated');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to update webhook', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->webhooks->delete($id);
            return $this->apiSuccess(null, 'Webhook deleted');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete webhook', 500, $e->getMessage());
        }
    }

    public function toggle(int $id): JsonResponse
    {
        try {
            $webhook = $this->webhooks->findOrFail($id);
            $webhook->update(['is_active' => !$webhook->is_active]);
            return $this->apiSuccess($webhook, 'Webhook toggled');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to toggle webhook', 500, $e->getMessage());
        }
    }

    public function regenerateSecret(int $id): JsonResponse
    {
        try {
            $webhook = $this->webhooks->findOrFail($id);
            $newSecret = bin2hex(random_bytes(16));
            $webhook->update(['secret' => $newSecret]);
            return $this->apiSuccess(['secret' => $newSecret], 'Secret regenerated');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to regenerate secret', 500, $e->getMessage());
        }
    }
}
