<?php

namespace Modules\Monitoring\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Monitoring\Services\SystemHealthService;
use Modules\Monitoring\Services\TenantBehaviorService;
use Modules\Monitoring\Services\ErrorManagementService;
use Modules\Monitoring\Services\ResourceInsightsService;
use Modules\Monitoring\Services\AdminToolsService;
use Modules\Monitoring\Services\DeveloperToolsService;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    protected $systemHealthService;
    protected $tenantBehaviorService;
    protected $errorManagementService;
    protected $resourceInsightsService;
    protected $adminToolsService;
    protected $developerToolsService;

    public function __construct(
        SystemHealthService $systemHealthService,
        TenantBehaviorService $tenantBehaviorService,
        ErrorManagementService $errorManagementService,
        ResourceInsightsService $resourceInsightsService,
        AdminToolsService $adminToolsService,
        DeveloperToolsService $developerToolsService
    ) {
        $this->systemHealthService = $systemHealthService;
        $this->tenantBehaviorService = $tenantBehaviorService;
        $this->errorManagementService = $errorManagementService;
        $this->resourceInsightsService = $resourceInsightsService;
        $this->adminToolsService = $adminToolsService;
        $this->developerToolsService = $developerToolsService;
    }

    /**
     * Main monitoring dashboard
     */
    public function index()
    {
        $title = "Monitoring Dashboard";
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => 'Monitoring Dashboard'],
        ];

        // Get overview data
        $systemHealth = $this->systemHealthService->getOverview();
        $tenantStats = $this->tenantBehaviorService->getOverview();
        $errorStats = $this->errorManagementService->getOverview();
        $resourceStats = $this->resourceInsightsService->getOverview();

        return view('landlord.monitoring.dashboard', compact(
            'title', 'breadcrumbs', 'systemHealth', 'tenantStats', 'errorStats', 'resourceStats'
        ));
    }

    /**
     * System Health monitoring
     */
    public function systemHealth()
    {
        $title = "System Health";
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => 'Monitoring', 'link' => route('landlord.monitoring.index')],
            ['text' => 'System Health'],
        ];

        return view('landlord.monitoring.system-health', compact('title', 'breadcrumbs'));
    }

    /**
     * Tenant Behavior monitoring
     */
    public function tenantBehavior()
    {
        $title = "Tenant Behavior";
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => 'Monitoring', 'link' => route('landlord.monitoring.index')],
            ['text' => 'Tenant Behavior'],
        ];

        return view('landlord.monitoring.tenant-behavior', compact('title', 'breadcrumbs'));
    }

    /**
     * Error Management
     */
    public function errorManagement()
    {
        $title = "Error Management";
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => 'Monitoring', 'link' => route('landlord.monitoring.index')],
            ['text' => 'Error Management'],
        ];

        return view('landlord.monitoring.error-management', compact('title', 'breadcrumbs'));
    }

    /**
     * Resource Insights
     */
    public function resourceInsights()
    {
        $title = "Resource Insights";
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => 'Monitoring', 'link' => route('landlord.monitoring.index')],
            ['text' => 'Resource Insights'],
        ];

        return view('landlord.monitoring.resource-insights', compact('title', 'breadcrumbs'));
    }

    /**
     * Admin Tools
     */
    public function adminTools()
    {
        $title = "Admin Tools";
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => 'Monitoring', 'link' => route('landlord.monitoring.index')],
            ['text' => 'Admin Tools'],
        ];

        return view('landlord.monitoring.admin-tools', compact('title', 'breadcrumbs'));
    }

    /**
     * Developer Tools
     */
    public function developerTools()
    {
        $title = "Developer Tools";
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => 'Monitoring', 'link' => route('landlord.monitoring.index')],
            ['text' => 'Developer Tools'],
        ];

        return view('landlord.monitoring.developer-tools', compact('title', 'breadcrumbs'));
    }

    /**
     * Tenant-specific monitoring
     */
    public function tenantMonitoring($tenantId)
    {
        $tenant = \Modules\Tenant\Entities\Tenant::find($tenantId);
        
        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $title = "Monitoring - " . $tenant->name;
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => 'Monitoring', 'link' => route('landlord.monitoring.index')],
            ['text' => $tenant->name],
        ];

        // Get tenant-specific data
        $systemHealth = $this->systemHealthService->getTenantHealth($tenantId);
        $behavior = $this->tenantBehaviorService->getTenantBehavior($tenantId);
        $errors = $this->errorManagementService->getTenantErrors($tenantId);
        $resources = $this->resourceInsightsService->getTenantResources($tenantId);

        return view('landlord.monitoring.tenant-specific', compact(
            'title', 'breadcrumbs', 'tenant', 'systemHealth', 'behavior', 'errors', 'resources'
        ));
    }

    /**
     * API endpoints for real-time data
     */
    public function getSystemHealthData()
    {
        return response()->json($this->systemHealthService->getRealTimeData());
    }

    public function getTenantBehaviorData()
    {
        return response()->json($this->tenantBehaviorService->getRealTimeData());
    }

    public function getErrorData()
    {
        return response()->json($this->errorManagementService->getRealTimeData());
    }

    public function getResourceData()
    {
        return response()->json($this->resourceInsightsService->getRealTimeData());
    }

    /**
     * Run data consistency check
     */
    public function runConsistencyCheck()
    {
        $result = $this->adminToolsService->runConsistencyCheck();
        return response()->json($result);
    }

    /**
     * Get migration status for all tenants
     */
    public function getMigrationStatus()
    {
        $status = $this->developerToolsService->getMigrationStatus();
        return response()->json($status);
    }
}
