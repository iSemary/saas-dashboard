<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WebhookService;
use Illuminate\Http\Request;

class WebhookApiController extends Controller
{
    public function __construct(protected WebhookService $webhookService) {}

    public function index(Request $request)
    {
        try {
            $webhooks = $this->webhookService->listByUser($request->user('api')->id);
            return response()->json(['data' => $webhooks]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'url' => 'required|url|max:500',
                'secret' => 'nullable|string|max:255',
                'events' => 'nullable|array',
                'status' => 'nullable|in:active,inactive',
                'timeout' => 'nullable|integer|min:1|max:300',
                'retry_count' => 'nullable|integer|min:0|max:10',
                'headers' => 'nullable|array',
            ]);

            $validated['created_by'] = $request->user('api')->id;

            $webhook = $this->webhookService->create($validated);

            return response()->json([
                'data' => $webhook->load('creator'),
                'message' => translate('message.webhook_created')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => translate('message.validation_failed'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $webhook = $this->webhookService->show($id);
            return response()->json(['data' => $webhook]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.resource_not_found'),
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if (!$this->webhookService->isOwner($id, $request->user('api')->id)) {
                return response()->json(['message' => translate('auth.unauthorized')], 403);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'url' => 'sometimes|required|url|max:500',
                'secret' => 'nullable|string|max:255',
                'events' => 'nullable|array',
                'status' => 'nullable|in:active,inactive',
                'timeout' => 'nullable|integer|min:1|max:300',
                'retry_count' => 'nullable|integer|min:0|max:10',
                'headers' => 'nullable|array',
            ]);

            $webhook = $this->webhookService->update($id, $validated);

            return response()->json([
                'data' => $webhook->load('creator'),
                'message' => translate('message.webhook_updated')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            if (!$this->webhookService->isOwner($id, request()->user('api')->id)) {
                return response()->json(['message' => translate('auth.unauthorized')], 403);
            }

            $this->webhookService->delete($id);

            return response()->json([
                'message' => translate('message.webhook_deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function test(Request $request, $id)
    {
        try {
            $request->validate([
                'payload' => 'nullable|array',
            ]);

            $response = $this->webhookService->test(
                $id,
                $request->user('api')->id,
                $request->get('payload', [])
            );

            return response()->json([
                'data' => $response,
                'message' => translate('message.webhook_test_sent')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logs(Request $request, $id)
    {
        try {
            if (!$this->webhookService->isOwner($id, $request->user('api')->id)) {
                return response()->json(['message' => translate('auth.unauthorized')], 403);
            }

            $perPage = $request->get('per_page', 20);
            $logs = $this->webhookService->getLogs($id, $request->user('api')->id, $perPage);

            return response()->json([
                'data' => $logs->items(),
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
