<?php

namespace Modules\Sales\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Sales\Domain\Contracts\SalesOrderRepositoryInterface;
use Modules\Sales\Domain\Entities\SalesOrder;

class SalesOrderRepository implements SalesOrderRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SalesOrder::with(['cashier', 'installment', 'delivery', 'steward']);

        if (!empty($filters['search'])) {
            $query->where('barcode', 'like', "%{$filters['search']}%");
        }
        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['pay_method'])) {
            $query->where('pay_method', $filters['pay_method']);
        }
        if (!empty($filters['order_type'])) {
            $query->where('order_type', $filters['order_type']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): SalesOrder
    {
        return SalesOrder::with(['cashier', 'installment', 'delivery', 'steward', 'clients'])->findOrFail($id);
    }

    public function findByBarcode(string $barcode): ?SalesOrder
    {
        return SalesOrder::where('barcode', $barcode)->first();
    }

    public function create(array $data): SalesOrder
    {
        return SalesOrder::create($data);
    }

    public function update(int $id, array $data): SalesOrder
    {
        $order = SalesOrder::findOrFail($id);
        $order->update($data);
        return $order->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) SalesOrder::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return SalesOrder::whereIn('id', $ids)->delete();
    }

    public function getDailySummary(?string $date, ?int $branchId): array
    {
        $date = $date ?? now()->toDateString();
        $query = SalesOrder::whereDate('created_at', $date);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $orders = $query->get();

        return [
            'date'         => $date,
            'total_orders' => $orders->count(),
            'total_revenue'=> (float) $orders->sum('total_price'),
            'total_paid'   => (float) $orders->sum('amount_paid'),
            'by_method'    => $orders->groupBy('pay_method')
                ->map(fn($g) => ['count' => $g->count(), 'revenue' => (float) $g->sum('total_price')])
                ->toArray(),
        ];
    }
}
