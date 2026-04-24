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
                'message' => 'Failed to retrieve webhooks',
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
                'message' => 'Webhook created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create webhook',
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
                'message' => 'Webhook not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if (!$this->webhookService->isOwner($id, $request->user('api')->id)) {
                return response()->json(['message' => 'Unauthorized'], 403);
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
                'message' => 'Webhook updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update webhook',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            if (!$this->webhookService->isOwner($id, request()->user('api')->id)) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $this->webhookService->delete($id);

            return response()->json([
                'message' => 'Webhook deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete webhook',
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
                'message' => 'Test webhook sent'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send test webhook',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logs(Request $request, $id)
    {
        try {
            if (!$this->webhookService->isOwner($id, $request->user('api')->id)) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $perPage = $request->get('per_page', 20);
            $logs = $this->webhookService->getLogs($id, $request->user('api')->id, $perPage);

            return response()->json([
                'data' => [
                    'data' => $logs->items(),
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve webhook logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
