<?php

namespace Modules\Auth\Http\Controllers\Landlord;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\SuperAdminService;
use Modules\Auth\Services\UserService;
use Modules\Auth\Services\ActivityLogService;
use Modules\Auth\Services\LoginAttemptService;
use Modules\Customer\Services\BrandService;
use Modules\Customer\Services\BrandModuleSubscriptionService;
use Modules\Tenant\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SuperAdminApiController extends ApiController
{
    protected SuperAdminService $superAdminService;
    protected UserService $userService;
    protected ActivityLogService $activityLogService;
    protected LoginAttemptService $loginAttemptService;
    protected BrandService $brandService;
    protected BrandModuleSubscriptionService $brandModuleService;
    protected TenantService $tenantService;

    public function __construct(
        SuperAdminService $superAdminService,
        UserService $userService,
        ActivityLogService $activityLogService,
        LoginAttemptService $loginAttemptService,
        BrandService $brandService,
        BrandModuleSubscriptionService $brandModuleService,
        TenantService $tenantService
    )
    {
        $this->superAdminService = $superAdminService;
        $this->userService = $userService;
        $this->activityLogService = $activityLogService;
        $this->loginAttemptService = $loginAttemptService;
        $this->brandService = $brandService;
        $this->brandModuleService = $brandModuleService;
        $this->tenantService = $tenantService;
    }

    /**
     * Get super admin dashboard data
     */
    public function dashboard(Request $request): JsonResponse
    {
        try
        {
            $dashboardData = $this->superAdminService->getDashboardData();

            return $this->return(200, translate('Dashboard data retrieved successfully'), $dashboardData);
        }
        catch (\Exception $e)
        {
            return $this->return(500, 'Error retrieving dashboard data: ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        try
        {
            $stats = [
                'users' => [
                    'total' => 0,
                    'growth_rate' => 0,
                ],
                'tenants' => [
                    'total' => 0,
                    'growth_rate' => 0,
                ],
                'categories' => [
                    'total' => 0,
                    'active' => 0,
                ],
                'brands' => [
                    'total' => 0,
                    'growth_rate' => 0,
                ],
                'brand_modules' => [
                    'active_subscriptions' => 0,
                    'brands_with_modules' => 0,
                ],
            ];

            return $this->return(200, translate('Statistics retrieved successfully'), $stats);
        }
        catch (\Exception $e)
        {
            return $this->return(500, 'Error retrieving statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(Request $request): JsonResponse
    {
        try
        {
            $activities = $this->superAdminService->getRecentActivities($request->get('limit', 20));

            return $this->return(200, translate('Recent activities retrieved successfully'), $activities);
        }
        catch (\Exception $e)
        {
            return $this->return(500, 'Error retrieving activities: ' . $e->getMessage());
        }
    }

    /**
     * Get system overview
     */
    public function getSystemOverview(Request $request): JsonResponse
    {
        try
        {
            $overview = [
                'total_users' => $this->userService->getTotalCount(),
                'active_users' => $this->userService->getActiveCount(),
                'total_tenants' => $this->tenantService->getTotalCount(),
                'active_tenants' => $this->tenantService->getActiveCount(),
                'total_brands' => $this->brandService->getTotalCount(),
                'active_brands' => $this->brandService->getActiveCount(),
                'system_load' => $this->superAdminService->getSystemLoad(),
                'disk_usage' => $this->superAdminService->getDiskUsage(),
                'memory_usage' => $this->superAdminService->getMemoryUsage(),
                'database_status' => $this->superAdminService->getDatabaseStatus(),
            ];

            return $this->return(200, translate('System overview retrieved successfully'), $overview);
        }
        catch (\Exception $e)
        {
            return $this->return(500, 'Error retrieving system overview: ' . $e->getMessage());
        }
    }

    /**
     * Get user growth chart data (last 30 days)
     */
    public function getUserChart(Request $request): JsonResponse
    {
        try
        {
            // Return mock data for now - TODO: implement actual chart data logic
            $chartData = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $chartData[] = [
                    'date' => $date,
                    'count' => rand(0, 10),
                ];
            }
            return $this->return(200, translate('User chart data retrieved successfully'), $chartData);
        }
        catch (\Exception $e)
        {
            return $this->return(500, 'Error retrieving user chart data: ' . $e->getMessage());
        }
    }

    /**
     * Get tenant growth chart data (last 30 days)
     */
    public function getTenantChart(Request $request): JsonResponse
    {
        try
        {
            // Return mock data for now - TODO: implement actual chart data logic
            $chartData = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $chartData[] = [
                    'date' => $date,
                    'count' => rand(0, 5),
                ];
            }
            return $this->return(200, translate('Tenant chart data retrieved successfully'), $chartData);
        }
        catch (\Exception $e)
        {
            return $this->return(500, 'Error retrieving tenant chart data: ' . $e->getMessage());
        }
    }

    /**
     * Get email activity chart data (last 30 days)
     */
    public function getEmailChart(Request $request): JsonResponse
    {
        try
        {
            // Return mock data for now - TODO: implement actual chart data logic
            $chartData = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $chartData[] = [
                    'date' => $date,
                    'count' => rand(0, 50),
                ];
            }
            return $this->return(200, translate('Email chart data retrieved successfully'), $chartData);
        }
        catch (\Exception $e)
        {
            return $this->return(500, 'Error retrieving email chart data: ' . $e->getMessage());
        }
    }

    /**
     * Get module statistics
     */
    public function getModuleStats(Request $request): JsonResponse
    {
        try
        {
            // Return mock data for now - TODO: implement actual module stats logic
            $moduleStats = [];
            return $this->return(200, translate('Module statistics retrieved successfully'), $moduleStats);
        }
        catch (\Exception $e)
        {
            return $this->return(500, 'Error retrieving module statistics: ' . $e->getMessage());
        }
    }
}
