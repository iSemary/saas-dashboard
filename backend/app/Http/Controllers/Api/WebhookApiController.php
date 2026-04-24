<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebhookApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $webhooks = Webhook::with('creator')
                ->where('created_by', $request->user('api')->id)
                ->orderBy('created_at', 'desc')
                ->get();

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
            $validated['status'] = $validated['status'] ?? 'active';
            $validated['timeout'] = $validated['timeout'] ?? 30;
            $validated['retry_count'] = $validated['retry_count'] ?? 3;
            $validated['secret'] = $validated['secret'] ?? Str::random(32);

            $webhook = Webhook::create($validated);

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
            $webhook = Webhook::with(['creator', 'logs' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(50);
            }])->findOrFail($id);
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
            $webhook = Webhook::findOrFail($id);

            // Check ownership
            if ($webhook->created_by !== $request->user('api')->id) {
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

            $webhook->update($validated);

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
            $webhook = Webhook::findOrFail($id);

            // Check ownership
            if ($webhook->created_by !== request()->user('api')->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $webhook->delete();

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

            $webhook = Webhook::findOrFail($id);

            // Check ownership
            if ($webhook->created_by !== $request->user('api')->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $payload = $request->get('payload', ['test' => true, 'timestamp' => now()->toIso8601String()]);
            
            // Send test webhook
            $response = $this->sendWebhook($webhook, 'test', $payload);

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
            $webhook = Webhook::findOrFail($id);

            // Check ownership
            if ($webhook->created_by !== $request->user('api')->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $perPage = $request->get('per_page', 20);
            $logs = WebhookLog::where('webhook_id', $id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

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

    protected function sendWebhook(Webhook $webhook, string $event, array $payload)
    {
        $headers = $webhook->headers ?? [];
        $headers['Content-Type'] = 'application/json';
        $headers['User-Agent'] = 'SaaS-Dashboard-Webhook/1.0';

        // Add signature if secret is set
        if ($webhook->secret) {
            $signature = hash_hmac('sha256', json_encode($payload), $webhook->secret);
            $headers['X-Webhook-Signature'] = $signature;
        }

        try {
            $response = Http::timeout($webhook->timeout)
                ->withHeaders($headers)
                ->post($webhook->url, $payload);

            // Log the webhook
            WebhookLog::create([
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
            // Log failed webhook
            WebhookLog::create([
                'webhook_id' => $webhook->id,
                'event' => $event,
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
