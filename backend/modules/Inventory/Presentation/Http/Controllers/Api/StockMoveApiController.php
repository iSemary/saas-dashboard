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
            return $this->apiError('Failed to retrieve stock moves', 500, $e->getMessage());
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
            return $this->apiSuccess($move->load('warehouse'), 'Stock move created successfully', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create stock move', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->service->findStockMove($id));
        } catch (\Throwable $e) {
            return $this->apiError('Stock move not found', 404);
        }
    }

    public function confirm(int $id): JsonResponse
    {
        try {
            $move = $this->service->confirmStockMove($id);
            return $this->apiSuccess($move, 'Stock move confirmed');
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to confirm stock move', 500, $e->getMessage());
        }
    }

    public function complete(int $id): JsonResponse
    {
        try {
            $move = $this->service->completeStockMove($id);
            return $this->apiSuccess($move, 'Stock move completed');
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to complete stock move', 500, $e->getMessage());
        }
    }

    public function cancel(int $id): JsonResponse
    {
        try {
            $move = $this->service->cancelStockMove($id);
            return $this->apiSuccess($move, 'Stock move cancelled');
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to cancel stock move', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->deleteStockMove($id);
            return $this->apiSuccess(null, 'Stock move deleted');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete stock move', 500, $e->getMessage());
        }
    }
}
