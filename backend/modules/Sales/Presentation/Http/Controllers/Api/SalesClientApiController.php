<?php

namespace Modules\Sales\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sales\Application\Services\SalesClientService;

class SalesClientApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly SalesClientService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search']);
            return $this->apiPaginated($this->service->list($filters, (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve clients', 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'code'    => 'nullable|string|max:100',
                'phone'   => 'nullable|string|max:30',
                'address' => 'nullable|string',
                'gift'    => 'nullable|numeric|min:0',
            ]);
            $client = $this->service->create($request->all(), auth()->id());
            return $this->apiSuccess($client->load(['user', 'orders']), 'Client created successfully', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create client', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->service->findOrFail($id));
        } catch (\Throwable $e) {
            return $this->apiError('Client not found', 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'code'    => 'nullable|string|max:100',
                'phone'   => 'nullable|string|max:30',
                'address' => 'nullable|string',
                'gift'    => 'nullable|numeric|min:0',
            ]);
            $client = $this->service->update($id, $request->all());
            return $this->apiSuccess($client, 'Client updated successfully');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to update client', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return $this->apiSuccess(null, 'Client deleted successfully');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete client', 500, $e->getMessage());
        }
    }
}
