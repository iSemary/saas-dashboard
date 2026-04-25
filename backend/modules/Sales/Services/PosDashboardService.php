<?php

namespace Modules\Sales\Services;

use Illuminate\Support\Facades\DB;

class PosDashboardService
{
    public function getDashboardData(): array
    {
        return [
            'products_count' => DB::table('products')->count(),
            'orders_count' => DB::table('orders')->count(),
            'revenue_today' => DB::table('orders')->whereDate('created_at', today())->sum('total_amount'),
            'recent_orders' => DB::table('orders')->orderBy('created_at', 'desc')->limit(5)->get(),
            'daily_sales' => $this->getDailySales(),
            'orders_by_status' => $this->getOrdersByStatus(),
            'revenue_by_category' => $this->getRevenueByCategory(),
        ];
    }

    private function getDailySales(int $days = 14): array
    {
        return DB::table('orders')
            ->select(
                DB::raw("DATE_FORMAT(order_date, '%Y-%m-%d') as date"),
                DB::raw('COUNT(*) as orders'),
                DB::raw('COALESCE(SUM(total_amount), 0) as revenue')
            )
            ->where('order_date', '>=', now()->subDays($days)->toDateString())
            ->whereNotIn('status', ['cancelled'])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'orders' => $row->orders,
                'revenue' => (float) $row->revenue,
            ])
            ->toArray();
    }

    private function getOrdersByStatus(): array
    {
        return DB::table('orders')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status,
                'count' => $row->count,
            ])
            ->toArray();
    }

    private function getRevenueByCategory(): array
    {
        return DB::table('products')
            ->select('category', DB::raw('COUNT(*) as product_count'), DB::raw('COALESCE(SUM(stock_quantity * price), 0) as stock_value'))
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->where('is_active', true)
            ->groupBy('category')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category,
                'product_count' => $row->product_count,
                'stock_value' => (float) $row->stock_value,
            ])
            ->toArray();
    }
}
