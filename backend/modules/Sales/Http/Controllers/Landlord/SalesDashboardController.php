<?php

namespace Modules\Sales\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Modules\Customer\Services\BrandService;
use Modules\Customer\Services\BrandModuleSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesDashboardController extends Controller
{
    protected BrandService $brandService;
    protected BrandModuleSubscriptionService $brandModuleService;

    public function __construct(
        BrandService $brandService,
        BrandModuleSubscriptionService $brandModuleService
    ) 
    {
        $this->brandService = $brandService;
        $this->brandModuleService = $brandModuleService;
    }

    /**
     * Show Sales dashboard for specific brand.
     */
    public function show(int $brandId): View
    {
        try {
            $brand = $this->brandService->getById($brandId);
            if (!$brand) 
            {
                abort(404, translate('brand_not_found'));
            }

            // Check if brand has access to Sales module
            if (!$this->brandModuleService->hasActiveSubscription($brandId, 'sales')) 
            {
                abort(403, translate('brand_no_access_to_module') . ': Sales');
            }

            // Get Sales-specific data (placeholder for now)
            $salesStats = $this->getSalesStats($brandId);

            return view('sales.dashboard.show', compact('brand', 'salesStats'));
        } catch (\Exception $e) {
            abort(500, translate('error_loading_sales_dashboard') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get Sales statistics for the brand.
     */
    private function getSalesStats(int $brandId): array
    {
        // This would typically come from Sales-specific tables
        // For now, returning placeholder data
        return [
            'total_orders' => rand(200, 1000),
            'completed_orders' => rand(150, 800),
            'pending_orders' => rand(20, 100),
            'total_revenue' => rand(100000, 1500000),
            'avg_order_value' => rand(200, 800),
            'total_products' => rand(50, 300),
            'quotes_sent' => rand(100, 500),
            'conversion_rate' => rand(15, 35),
        ];
    }

    /**
     * Get Sales dashboard data via AJAX.
     */
    public function getData(Request $request)
    {
        try {
            $brandId = $request->get('brandId');
            $brand = $this->brandService->getById($brandId);
            
            if (!$brand || !$this->brandModuleService->hasActiveSubscription($brandId, 'sales')) 
            {
                return response()->json([
                    'success' => false,
                    'message' => translate('unauthorized_access'),
                ], 403);
            }

            $salesStats = $this->getSalesStats($brandId);
            
            return response()->json([
                'success' => true,
                'data' => $salesStats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('error_loading_data'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
