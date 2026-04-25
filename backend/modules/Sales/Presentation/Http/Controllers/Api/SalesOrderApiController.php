<?php

namespace Modules\Sales\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sales\Application\Services\SalesOrderService;
use Modules\Sales\Domain\Strategies\OrderType\DeliveryOrderStrategy;
use Modules\Sales\Domain\Strategies\OrderType\DineInOrderStrategy;
use Modules\Sales\Domain\Strategies\OrderType\TakeawayOrderStrategy;
use Modules\Sales\Domain\Strategies\Payment\CardPaymentStrategy;
use Modules\Sales\Domain\Strategies\Payment\CashPaymentStrategy;
use Modules\Sales\Domain\Strategies\Payment\InstallmentPaymentStrategy;

class SalesOrderApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly SalesOrderService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'branch_id', 'status', 'pay_method', 'order_type', 'date_from', 'date_to']);
            return $this->apiPaginated($this->service->list($filters, (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve orders', 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'products'        => 'required|array|min:1',
                'products.*.product_id' => 'required|integer',
                'products.*.quantity'   => 'required|numeric|min:0.01',
                'total_price'     => 'required|numeric|min:0',
                'amount_paid'     => 'required|numeric|min:0',
                'pay_method'      => 'required|in:cash,card,installment,transfer',
                'order_type'      => 'required|in:takeaway,dine_in,delivery,steward',
                'branch_id'       => 'nullable|integer',
            ]);

            $data = $request->all();
            $paymentStrategy = match($data['pay_method']) {
                'card'        => new CardPaymentStrategy(),
                'installment' => new InstallmentPaymentStrategy(),
                default       => new CashPaymentStrategy(),
            };
            $orderTypeStrategy = match($data['order_type']) {
                'delivery' => new DeliveryOrderStrategy(),
                'dine_in'  => new DineInOrderStrategy(),
                default    => new TakeawayOrderStrategy(),
            };

            $order = $this->service->createOrder($data, auth()->id(), $paymentStrategy, $orderTypeStrategy);
            return $this->apiSuccess($order->load(['installment', 'delivery', 'steward']), 'Order created successfully', 201);
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create order', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->service->findOrFail($id));
        } catch (\Throwable $e) {
            return $this->apiError('Order not found', 404);
        }
    }

    public function cancel(int $id): JsonResponse
    {
        try {
            $order = $this->service->cancelOrder($id, auth()->id());
            return $this->apiSuccess($order, 'Order cancelled and stock restored');
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to cancel order', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return $this->apiSuccess(null, 'Order deleted successfully');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete order', 500, $e->getMessage());
        }
    }

    public function summary(Request $request): JsonResponse
    {
        try {
            $summary = $this->service->getDailySummary(
                $request->get('date'),
                $request->get('branch_id'),
            );
            return $this->apiSuccess($summary);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get summary', 500, $e->getMessage());
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $request->validate(['ids' => 'required|array']);
            $count = $this->service->bulkDelete($request->ids);
            return $this->apiSuccess(null, "{$count} orders deleted successfully");
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete orders', 500, $e->getMessage());
        }
    }
}
