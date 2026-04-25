<?php

namespace Modules\POS\Application\Services;

use Illuminate\Support\Facades\DB;
use Modules\POS\Domain\Entities\Product;
use Modules\POS\Domain\Entities\ProductStock;

class PosDashboardService
{
    public function getDashboardData(): array
    {
        return [
            'products_count'     => Product::count(),
            'out_of_stock_count' => $this->getOutOfStockCount(),
            'stock_value'        => $this->getTotalStockValue(),
            'offer_prices_count' => DB::table('pos_offer_prices')->whereNull('deleted_at')->count(),
            'daily_sales'        => $this->getDailySales(),
            'top_products'       => $this->getTopProducts(),
            'stock_by_category'  => $this->getStockByCategory(),
        ];
    }

    private function getOutOfStockCount(): int
    {
        return Product::whereDoesntHave('productStocks', fn($q) => $q->where('quantity', '>', 0))->count();
    }

    private function getTotalStockValue(): float
    {
        return (float) DB::table('pos_products as p')
            ->join('pos_product_stocks as ps', 'p.id', '=', 'ps.product_id')
            ->whereNull('p.deleted_at')
            ->whereNull('ps.deleted_at')
            ->selectRaw('SUM(ps.quantity * p.sale_price) as value')
            ->value('value');
    }

    private function getDailySales(int $days = 14): array
    {
        return DB::table('sales_orders')
            ->whereNull('deleted_at')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as date, COUNT(*) as orders, COALESCE(SUM(total_price), 0) as revenue")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($row) => ['date' => $row->date, 'orders' => $row->orders, 'revenue' => (float) $row->revenue])
            ->toArray();
    }

    private function getTopProducts(int $limit = 5): array
    {
        return Product::withCount('productStocks')
            ->orderBy('ordered_count', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'sale_price', 'ordered_count', 'image'])
            ->toArray();
    }

    private function getStockByCategory(): array
    {
        return DB::table('pos_categories as c')
            ->leftJoin('pos_products as p', 'c.id', '=', 'p.category_id')
            ->leftJoin('pos_product_stocks as ps', 'p.id', '=', 'ps.product_id')
            ->whereNull('c.deleted_at')
            ->whereNull('p.deleted_at')
            ->selectRaw('c.name as category, COUNT(DISTINCT p.id) as product_count, COALESCE(SUM(ps.quantity * p.sale_price), 0) as stock_value')
            ->groupBy('c.id', 'c.name')
            ->get()
            ->map(fn($row) => ['category' => $row->category, 'product_count' => $row->product_count, 'stock_value' => (float) $row->stock_value])
            ->toArray();
    }
}
