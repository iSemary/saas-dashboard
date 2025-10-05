<?php

namespace Modules\HR\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Modules\Customer\Services\BrandService;
use Modules\Customer\Services\BrandModuleSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HRDashboardController extends Controller
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
     * Show HR dashboard for specific brand.
     */
    public function show(int $brandId): View
    {
        try {
            $brand = $this->brandService->getById($brandId);
            if (!$brand) 
            {
                abort(404, translate('brand_not_found'));
            }

            // Check if brand has access to HR module
            if (!$this->brandModuleService->hasActiveSubscription($brandId, 'hr')) 
            {
                abort(403, translate('brand_no_access_to_module') . ': HR');
            }

            // Get HR-specific data (placeholder for now)
            $hrStats = $this->getHRStats($brandId);

            return view('hr.dashboard.show', compact('brand', 'hrStats'));
        } catch (\Exception $e) {
            abort(500, translate('error_loading_hr_dashboard') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get HR statistics for the brand.
     */
    private function getHRStats(int $brandId): array
    {
        // This would typically come from HR-specific tables
        // For now, returning placeholder data
        return [
            'total_employees' => rand(50, 200),
            'active_employees' => rand(40, 180),
            'pending_leaves' => rand(5, 15),
            'total_departments' => rand(3, 10),
            'avg_attendance' => rand(80, 98),
            'recent_hires' => rand(2, 8),
            'pending_payrolls' => rand(1, 5),
        ];
    }

    /**
     * Get HR dashboard data via AJAX.
     */
    public function getData(Request $request)
    {
        try {
            $brandId = $request->get('brandId');
            $brand = $this->brandService->getById($brandId);
            
            if (!$brand || !$this->brandModuleService->hasActiveSubscription($brandId, 'hr')) 
            {
                return response()->json([
                    'success' => false,
                    'message' => translate('unauthorized_access'),
                ], 403);
            }

            $hrStats = $this->getHRStats($brandId);
            
            return response()->json([
                'success' => true,
                'data' => $hrStats,
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
