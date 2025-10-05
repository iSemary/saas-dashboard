<?php

namespace Modules\Accounting\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Modules\Customer\Services\BrandService;
use Modules\Customer\Services\BrandModuleSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingDashboardController extends Controller
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
     * Show Accounting dashboard for specific brand.
     */
    public function show(int $brandId): View
    {
        try {
            $brand = $this->brandService->getById($brandId);
            if (!$brand) 
            {
                abort(404, translate('brand_not_found'));
            }

            // Check if brand has access to Accounting module
            if (!$this->brandModuleService->hasActiveSubscription($brandId, 'accounting')) 
            {
                abort(403, translate('brand_no_access_to_module') . ': Accounting');
            }

            // Get Accounting-specific data (placeholder for now)
            $accountingStats = $this->getAccountingStats($brandId);

            return view('accounting.dashboard.show', compact('brand', 'accountingStats'));
        } catch (\Exception $e) {
            abort(500, translate('error_loading_accounting_dashboard') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get Accounting statistics for the brand.
     */
    private function getAccountingStats(int $brandId): array
    {
        // This would typically come from Accounting-specific tables
        // For now, returning placeholder data
        return [
            'total_revenue' => rand(100000, 2000000),
            'total_expenses' => rand(80000, 1500000),
            'net_profit' => rand(20000, 500000),
            'accounts_receivable' => rand(10000, 200000),
            'accounts_payable' => rand(5000, 100000),
            'cash_balance' => rand(50000, 500000),
            'pending_transactions' => rand(10, 50),
            'journal_entries_this_month' => rand(50, 200),
        ];
    }

    /**
     * Get Accounting dashboard data via AJAX.
     */
    public function getData(Request $request)
    {
        try {
            $brandId = $request->get('brandId');
            $brand = $this->brandService->getById($brandId);
            
            if (!$brand || !$this->brandModuleService->hasActiveSubscription($brandId, 'accounting')) 
            {
                return response()->json([
                    'success' => false,
                    'message' => translate('unauthorized_access'),
                ], 403);
            }

            $accountingStats = $this->getAccountingStats($brandId);
            
            return response()->json([
                'success' => true,
                'data' => $accountingStats,
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
