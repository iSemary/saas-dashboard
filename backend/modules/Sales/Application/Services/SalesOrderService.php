<?php

namespace Modules\Sales\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\POS\Domain\Contracts\ProductRepositoryInterface;
use Modules\POS\Domain\Contracts\ProductStockRepositoryInterface;
use Modules\POS\Domain\Enums\StockModelType;
use Modules\POS\Domain\Strategies\Stock\DecrementStockStrategy;
use Modules\POS\Domain\Strategies\Stock\IncrementStockStrategy;
use Modules\Sales\Domain\Contracts\SalesOrderRepositoryInterface;
use Modules\Sales\Domain\Entities\SalesOrder;
use Modules\Sales\Domain\Entities\SalesOrderInstallment;
use Modules\Sales\Domain\Entities\SalesDelivery;
use Modules\Sales\Domain\Entities\SalesOrderTouch;
use Modules\Sales\Domain\Entities\SalesOrderSteward;
use Modules\Sales\Domain\Strategies\Payment\PaymentStrategyInterface;
use Modules\Sales\Domain\Strategies\OrderType\OrderTypeStrategyInterface;

class SalesOrderService
{
    public function __construct(
        private readonly SalesOrderRepositoryInterface $repository,
        private readonly ProductStockRepositoryInterface $stockRepository,
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): SalesOrder
    {
        return $this->repository->findOrFail($id);
    }

    public function getDailySummary(?string $date = null, ?int $branchId = null): array
    {
        return $this->repository->getDailySummary($date, $branchId);
    }

    public function createOrder(array $data, int $userId, PaymentStrategyInterface $paymentStrategy, OrderTypeStrategyInterface $orderTypeStrategy): SalesOrder
    {
        return DB::transaction(function () use ($data, $userId, $paymentStrategy, $orderTypeStrategy) {
            $paymentStrategy->validate($data);
            $data = $paymentStrategy->process($data);
            $data = $orderTypeStrategy->prepare($data);
            $data['barcode'] = strtoupper(Str::random(10));
            $data['status'] = 'completed';

            $order = $this->repository->create($data);

            // Decrement stock for each product in the order
            foreach (($data['products'] ?? []) as $item) {
                $decrement = new DecrementStockStrategy($this->stockRepository);
                $decrement->execute(
                    productId: $item['product_id'],
                    branchId:  $data['branch_id'] ?? null,
                    amount:    $item['quantity'],
                    model:     StockModelType::Order,
                    objectId:  $order->id,
                    mainPrice: $item['unit_price'] ?? 0,
                    totalPrice:$item['total_price'] ?? 0,
                    createdBy: $userId,
                );
            }

            // Handle order type extras
            if ($orderTypeStrategy->getType() === 'installment' || ($data['pay_method'] ?? '') === 'installment') {
                SalesOrderInstallment::create([
                    'order_id'         => $order->id,
                    'installment_type' => $data['installment_type'] ?? null,
                    'total_months'     => $data['total_months'] ?? 1,
                    'paid_months'      => 0,
                    'monthly_amount'   => $data['monthly_amount'] ?? 0,
                ]);
            }

            if ($orderTypeStrategy->getType() === 'delivery') {
                SalesDelivery::create([
                    'order_id'     => $order->id,
                    'full_name'    => $data['delivery_name'] ?? '',
                    'phone_number' => $data['delivery_phone'] ?? null,
                    'address'      => $data['delivery_address'] ?? null,
                    'delivery_man' => $data['delivery_man'] ?? null,
                    'delivery_fee' => $data['delivery_fee'] ?? 0,
                ]);
            }

            if ($orderTypeStrategy->getType() === 'dine_in') {
                SalesOrderTouch::create([
                    'order_id'    => $order->id,
                    'order_type'  => 'dine_in',
                    'table_number'=> $data['table_number'] ?? null,
                    'service_fee' => $data['service_fee'] ?? 0,
                ]);
            }

            if ($orderTypeStrategy->getType() === 'steward') {
                SalesOrderSteward::create([
                    'order_id'    => $order->id,
                    'cashier_id'  => $userId,
                    'steward_id'  => $data['steward_id'] ?? null,
                    'order_number'=> strtoupper(Str::random(6)),
                    'branch_id'   => $data['branch_id'] ?? null,
                    'status'      => 'pending',
                    'notes'       => $data['notes'] ?? null,
                ]);
            }

            return $order;
        });
    }

    public function cancelOrder(int $id, int $userId): SalesOrder
    {
        return DB::transaction(function () use ($id, $userId) {
            $order = $this->repository->findOrFail($id);

            if ($order->status === 'cancelled') {
                throw new \DomainException('Order is already cancelled.');
            }

            // Restore stock
            foreach (($order->products ?? []) as $item) {
                $increment = new IncrementStockStrategy($this->stockRepository);
                $increment->execute(
                    productId: $item['product_id'],
                    branchId:  $order->branch_id,
                    amount:    $item['quantity'],
                    model:     StockModelType::Order,
                    objectId:  $order->id,
                    mainPrice: $item['unit_price'] ?? 0,
                    totalPrice:$item['total_price'] ?? 0,
                    createdBy: $userId,
                );
            }

            return $this->repository->update($id, ['status' => 'cancelled']);
        });
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function bulkDelete(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }
}
