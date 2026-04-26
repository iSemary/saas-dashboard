<?php

namespace Modules\Inventory\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Application\Services\InventoryService;

class WarehouseApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly InventoryService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            if ($request->boolean('all')) {
                return $this->apiSuccess($this->service->allWarehouses());
            }
            $filters = $request->only(['search', 'is_active']);
            return $this->apiPaginated($this->service->listWarehouses($filters, (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name'       => 'required|string|max:255',
                'code'       => 'required|string|max:50|unique:warehouses,code',
                'is_active'  => 'boolean',
                'is_default' => 'boolean',
                'manager_id' => 'nullable|integer',
            ]);
            $warehouse = $this->service->createWarehouse($request->all(), auth()->id());
            return $this->apiSuccess($warehouse, translate('message.created_successfully'), 201);
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->service->findWarehouse($id));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.resource_not_found'), 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'name'       => 'sometimes|string|max:255',
                'is_active'  => 'boolean',
                'is_default' => 'boolean',
                'manager_id' => 'nullable|integer',
            ]);
            $warehouse = $this->service->updateWarehouse($id, $request->all());
            return $this->apiSuccess($warehouse, translate('message.updated_successfully'));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->deleteWarehouse($id);
            return $this->apiSuccess(null, translate('message.deleted_successfully'));
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function stockSummary(Request $request, int $id): JsonResponse
    {
        try {
            $summary = $this->service->getStockSummary($id, $request->get('product_id'));
            return $this->apiSuccess($summary);
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }
}
