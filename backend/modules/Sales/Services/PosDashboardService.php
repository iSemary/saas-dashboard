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
            'revenue_today' => DB::table('orders')->whereDate('created_at', today())->sum('total'),
            'recent_orders' => DB::table('orders')->orderBy('created_at', 'desc')->limit(5)->get(),
        ];
    }
}
