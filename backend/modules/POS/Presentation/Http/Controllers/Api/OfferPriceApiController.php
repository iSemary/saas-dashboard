<?php

namespace Modules\POS\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\POS\Application\Services\OfferPriceService;
use Modules\POS\Presentation\Http\Requests\StoreOfferPriceRequest;

class OfferPriceApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly OfferPriceService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['product_id', 'branch_id']);
            return $this->apiPaginated($this->service->list($filters, (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function store(StoreOfferPriceRequest $request): JsonResponse
    {
        try {
            $offer = $this->service->create($request->validated(), auth()->id());
            return $this->apiSuccess($offer->load('product'), translate('message.created_successfully'), 201);
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->service->findOrFail($id));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.resource_not_found'), 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'amount'      => 'sometimes|numeric|min:0',
                'buyer_name'  => 'nullable|string|max:255',
                'reduce_stock'=> 'boolean',
            ]);
            $offer = $this->service->update($id, $request->validated());
            return $this->apiSuccess($offer, translate('message.updated_successfully'));
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
