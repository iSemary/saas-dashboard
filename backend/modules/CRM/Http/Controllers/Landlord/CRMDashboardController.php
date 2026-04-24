<?php

namespace Modules\CRM\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Modules\Customer\Services\BrandService;
use Modules\Customer\Services\BrandModuleSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CRMDashboardController extends Controller
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
     * Show CRM dashboard for specific brand.
     */
    public function show(int $brandId): View
    {
        try {
            $brand = $this->brandService->getById($brandId);
            if (!$brand) 
            {
                abort(404, translate('brand_not_found'));
            }

            // Check if brand has access to CRM module
            if (!$this->brandModuleService->hasActiveSubscription($brandId, 'crm')) 
            {
                abort(403, translate('brand_no_access_to_module') . ': CRM');
            }

            // Get CRM-specific data (placeholder for now)
            $crmStats = $this->getCRMStats($brandId);

            return view('crm.dashboard.show', compact('brand', 'crmStats'));
        } catch (\Exception $e) {
            abort(500, translate('error_loading_crm_dashboard') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get CRM statistics for the brand.
     */
    private function getCRMStats(int $brandId): array
    {
        // This would typically come from CRM-specific tables
        // For now, returning placeholder data
        return [
            'total_leads' => rand(100, 500),
            'qualified_leads' => rand(50, 250),
            'converted_clients' => rand(20, 100),
            'total_opportunities' => rand(80, 350),
            'closed_won_value' => rand(50000, 500000),
            'avg_response_time' => rand(1, 24),
            'active_contacts' => rand(200, 800),
            'total_companies' => rand(100, 400),
        ];
    }

    /**
     * Get CRM dashboard data via AJAX.
     */
    public function getData(Request $request)
    {
        try {
            $brandId = $request->get('brandId');
            $brand = $this->brandService->getById($brandId);
            
            if (!$brand || !$this->brandModuleService->hasActiveSubscription($brandId, 'crm')) 
            {
                return response()->json([
                    'success' => false,
                    'message' => translate('unauthorized_access'),
                ], 403);
            }

            $crmStats = $this->getCRMStats($brandId);
            
            return response()->json([
                'success' => true,
                'data' => $crmStats,
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
