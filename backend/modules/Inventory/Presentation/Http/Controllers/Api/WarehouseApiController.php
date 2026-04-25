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
            return $this->apiError('Failed to retrieve warehouses', 500, $e->getMessage());
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
            return $this->apiSuccess($warehouse, 'Warehouse created successfully', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create warehouse', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->service->findWarehouse($id));
        } catch (\Throwable $e) {
            return $this->apiError('Warehouse not found', 404);
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
            return $this->apiSuccess($warehouse, 'Warehouse updated successfully');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to update warehouse', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->deleteWarehouse($id);
            return $this->apiSuccess(null, 'Warehouse deleted successfully');
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete warehouse', 500, $e->getMessage());
        }
    }

    public function stockSummary(Request $request, int $id): JsonResponse
    {
        try {
            $summary = $this->service->getStockSummary($id, $request->get('product_id'));
            return $this->apiSuccess($summary);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get stock summary', 500, $e->getMessage());
        }
    }
}
