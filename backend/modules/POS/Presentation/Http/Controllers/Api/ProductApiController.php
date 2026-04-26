<?php

namespace Modules\POS\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\POS\Application\DTOs\CreateProductData;
use Modules\POS\Application\DTOs\UpdateProductData;
use Modules\POS\Application\Services\ProductService;
use Modules\POS\Domain\Enums\StockDirection;
use Modules\POS\Presentation\Http\Requests\ChangeStockRequest;
use Modules\POS\Presentation\Http\Requests\StoreProductRequest;
use Modules\POS\Presentation\Http\Requests\UpdateProductRequest;

class ProductApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly ProductService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'category_id', 'sub_category_id', 'is_offer', 'expired', 'type']);
            $perPage = (int) $request->get('per_page', 15);
            return $this->apiPaginated($this->service->list($filters, $perPage));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $data = CreateProductData::fromRequest($request);
            $product = $this->service->create($data, auth()->id());
            return $this->apiSuccess($product->load(['category', 'subCategory', 'barcodes', 'tags']), translate('message.created_successfully'), 201);
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

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $data = UpdateProductData::fromRequest($request);
            $product = $this->service->update($id, $data, auth()->id());
            return $this->apiSuccess($product, translate('message.updated_successfully'));
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

    public function changeStock(ChangeStockRequest $request, int $id): JsonResponse
    {
        try {
            $direction = StockDirection::from($request->validated('direction'));
            $this->service->changeStock(
                productId: $id,
                amount:    (float) $request->validated('amount'),
                direction: $direction,
                branchId:  $request->validated('branch_id'),
                userId:    auth()->id(),
            );
            $stock = $this->service->getAvailableStock($id, $request->validated('branch_id'));
            return $this->apiSuccess(['available_stock' => $stock], translate('message.updated_successfully'));
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
            $count = $this->service->bulkDelete($request->ids);
            return $this->apiSuccess(null, "{$count} products deleted successfully");
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function searchByBarcode(string $barcode): JsonResponse
    {
        try {
            $product = $this->service->findByBarcode($barcode);
            if (!$product) return $this->apiError(translate('message.resource_not_found'), 404);
            return $this->apiSuccess($product->load(['category', 'barcodes']));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }
}
