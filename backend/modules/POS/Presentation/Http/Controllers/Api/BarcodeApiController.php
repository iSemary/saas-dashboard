<?php

namespace Modules\POS\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\POS\Application\Services\BarcodeService;
use Modules\POS\Presentation\Http\Requests\StoreBarcodeRequest;

class BarcodeApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly BarcodeService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'product_id']);
            return $this->apiPaginated($this->service->list($filters, (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function store(StoreBarcodeRequest $request): JsonResponse
    {
        try {
            $barcode = $this->service->create($request->validated(), auth()->id());
            return $this->apiSuccess($barcode->load('product'), translate('message.created_successfully'), 201);
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function search(string $barcode): JsonResponse
    {
        try {
            $result = $this->service->searchByNumber($barcode);
            if (!$result) return $this->apiError(translate('message.resource_not_found'), 404);
            return $this->apiSuccess($result->load(['product.category', 'product.productStocks']));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return $this->apiSuccess(null, translate('message.deleted_successfully'));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }
}
