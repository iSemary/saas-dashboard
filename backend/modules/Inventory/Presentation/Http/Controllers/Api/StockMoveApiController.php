<?php

namespace Modules\Inventory\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Application\Services\InventoryService;

class StockMoveApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly InventoryService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['warehouse_id', 'product_id', 'move_type', 'state', 'date_from', 'date_to']);
            return $this->apiPaginated($this->service->listStockMoves($filters, (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'reference'    => 'nullable|string',
                'product_id'   => 'required|integer',
                'warehouse_id' => 'required|integer|exists:warehouses,id',
                'move_type'    => 'required|in:in,out,transfer,adjust',
                'quantity'     => 'required|numeric|min:0.01',
                'unit_cost'    => 'nullable|numeric|min:0',
                'date'         => 'nullable|date',
                'description'  => 'nullable|string',
            ]);
            $move = $this->service->createStockMove($request->all(), auth()->id());
            return $this->apiSuccess($move->load('warehouse'), translate('message.created_successfully'), 201);
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->service->findStockMove($id));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.resource_not_found'), 404);
        }
    }

    public function confirm(int $id): JsonResponse
    {
        try {
            $move = $this->service->confirmStockMove($id);
            return $this->apiSuccess($move, translate('message.action_completed'));
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function complete(int $id): JsonResponse
    {
        try {
            $move = $this->service->completeStockMove($id);
            return $this->apiSuccess($move, translate('message.action_completed'));
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function cancel(int $id): JsonResponse
    {
        try {
            $move = $this->service->cancelStockMove($id);
            return $this->apiSuccess($move, translate('message.action_completed'));
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->deleteStockMove($id);
            return $this->apiSuccess(null, translate('message.deleted_successfully'));
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }
}
