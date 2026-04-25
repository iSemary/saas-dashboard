<?php

namespace Modules\POS\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\POS\Application\Services\DamagedService;
use Modules\POS\Presentation\Http\Requests\StoreDamagedRequest;

class DamagedApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly DamagedService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['product_id', 'branch_id']);
            return $this->apiPaginated($this->service->list($filters, (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve damaged records', 500, $e->getMessage());
        }
    }

    public function store(StoreDamagedRequest $request): JsonResponse
    {
        try {
            $record = $this->service->create($request->validated(), auth()->id());
            return $this->apiSuccess($record->load('product'), 'Damaged record created successfully', 201);
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create damaged record', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->service->findOrFail($id));
        } catch (\Throwable $e) {
            return $this->apiError('Record not found', 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return $this->apiSuccess(null, 'Damaged record deleted and stock restored');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete damaged record', 500, $e->getMessage());
        }
    }
}
