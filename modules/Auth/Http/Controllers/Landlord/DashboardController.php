<?php

namespace Modules\Auth\Http\Controllers\Landlord;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Localization\Services\LanguageService;
use Modules\Utilities\Services\CategoryService;
use Modules\Utilities\Services\TypeService;
use Modules\Utilities\Services\IndustryService;
use Modules\Email\Services\EmailTemplateService;
use Modules\Tenant\Services\TenantService;
use Modules\Auth\Services\UserService;

class DashboardController extends ApiController
{
    protected $languageService;
    protected $categoryService;
    protected $typeService;
    protected $industryService;
    protected $emailTemplateService;
    protected $tenantService;
    protected $userService;

    public function __construct(
        LanguageService $languageService,
        CategoryService $categoryService,
        TypeService $typeService,
        IndustryService $industryService,
        EmailTemplateService $emailTemplateService,
        TenantService $tenantService,
        UserService $userService
    ) {
        $this->languageService = $languageService;
        $this->categoryService = $categoryService;
        $this->typeService = $typeService;
        $this->industryService = $industryService;
        $this->emailTemplateService = $emailTemplateService;
        $this->tenantService = $tenantService;
        $this->userService = $userService;
    }

    public function index()
    {
        return view('landlord.dashboard.index', []);
    }

    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        try {
            $stats = [
                'users' => $this->getUserStats(),
                'tenants' => $this->getTenantStats(),
                'categories' => $this->getCategoryStats(),
                'types' => $this->getTypeStats(),
                'industries' => $this->getIndustryStats(),
                'email_templates' => $this->getEmailTemplateStats(),
                'languages' => $this->getLanguageStats(),
                'system' => $this->getSystemStats(),
            ];

            return $this->return(200, 'Dashboard statistics retrieved successfully', $stats);
        } catch (\Exception $e) {
            return $this->return(500, 'Error retrieving dashboard statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get user statistics
     */
    private function getUserStats()
    {
        $totalUsers = DB::connection('landlord')->table('users')->count();
        // Since users table doesn't have status column, we'll use email_verified_at as active indicator
        $activeUsers = DB::connection('landlord')->table('users')->whereNotNull('email_verified_at')->count();
        $newUsersThisMonth = DB::connection('landlord')->table('users')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total' => $totalUsers,
            'active' => $activeUsers,
            'new_this_month' => $newUsersThisMonth,
            'growth_rate' => $this->calculateGrowthRate('users', 'created_at')
        ];
    }

    /**
     * Get tenant statistics
     */
    private function getTenantStats()
    {
        $totalTenants = DB::connection('landlord')->table('tenants')->count();
        // Since tenants table doesn't have status column, we'll consider all non-deleted tenants as active
        $activeTenants = DB::connection('landlord')->table('tenants')->whereNull('deleted_at')->count();
        $newTenantsThisMonth = DB::connection('landlord')->table('tenants')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total' => $totalTenants,
            'active' => $activeTenants,
            'new_this_month' => $newTenantsThisMonth,
            'growth_rate' => $this->calculateGrowthRate('tenants', 'created_at')
        ];
    }

    /**
     * Get category statistics
     */
    private function getCategoryStats()
    {
        $totalCategories = DB::connection('landlord')->table('categories')->count();
        $activeCategories = DB::connection('landlord')->table('categories')->where('status', 'active')->count();

        return [
            'total' => $totalCategories,
            'active' => $activeCategories,
            'inactive' => $totalCategories - $activeCategories
        ];
    }

    /**
     * Get type statistics
     */
    private function getTypeStats()
    {
        $totalTypes = DB::connection('landlord')->table('types')->count();
        $activeTypes = DB::connection('landlord')->table('types')->where('status', 'active')->count();

        return [
            'total' => $totalTypes,
            'active' => $activeTypes,
            'inactive' => $totalTypes - $activeTypes
        ];
    }

    /**
     * Get industry statistics
     */
    private function getIndustryStats()
    {
        $totalIndustries = DB::connection('landlord')->table('industries')->count();
        $activeIndustries = DB::connection('landlord')->table('industries')->where('status', 'active')->count();

        return [
            'total' => $totalIndustries,
            'active' => $activeIndustries,
            'inactive' => $totalIndustries - $activeIndustries
        ];
    }

    /**
     * Get email template statistics
     */
    private function getEmailTemplateStats()
    {
        $totalTemplates = DB::connection('landlord')->table('email_templates')->count();
        $activeTemplates = DB::connection('landlord')->table('email_templates')->where('status', 'active')->count();

        return [
            'total' => $totalTemplates,
            'active' => $activeTemplates,
            'inactive' => $totalTemplates - $activeTemplates
        ];
    }

    /**
     * Get language statistics
     */
    private function getLanguageStats()
    {
        $totalLanguages = DB::connection('landlord')->table('languages')->count();
        // Since languages table doesn't have status column, we'll consider all non-deleted languages as active
        $activeLanguages = DB::connection('landlord')->table('languages')->whereNull('deleted_at')->count();

        return [
            'total' => $totalLanguages,
            'active' => $activeLanguages,
            'inactive' => $totalLanguages - $activeLanguages
        ];
    }

    /**
     * Get system statistics
     */
    private function getSystemStats()
    {
        $totalTranslations = DB::connection('landlord')->table('translations')->count();
        $totalEmailLogs = DB::connection('landlord')->table('email_logs')->count();
        $sentEmails = DB::connection('landlord')->table('email_logs')->where('status', 'sent')->count();
        $failedEmails = DB::connection('landlord')->table('email_logs')->where('status', 'failed')->count();

        return [
            'translations' => $totalTranslations,
            'email_logs' => $totalEmailLogs,
            'sent_emails' => $sentEmails,
            'failed_emails' => $failedEmails,
            'email_success_rate' => $totalEmailLogs > 0 ? round(($sentEmails / $totalEmailLogs) * 100, 2) : 0
        ];
    }

    /**
     * Get chart data for users over time
     */
    public function getUserChartData()
    {
        try {
            $data = DB::connection('landlord')->table('users')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return $this->return(200, 'User chart data retrieved successfully', $data->toArray());
        } catch (\Exception $e) {
            return $this->return(500, 'Error retrieving user chart data: ' . $e->getMessage());
        }
    }

    /**
     * Get chart data for tenants over time
     */
    public function getTenantChartData()
    {
        try {
            $data = DB::connection('landlord')->table('tenants')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return $this->return(200, 'Tenant chart data retrieved successfully', $data->toArray());
        } catch (\Exception $e) {
            return $this->return(500, 'Error retrieving tenant chart data: ' . $e->getMessage());
        }
    }

    /**
     * Get chart data for email logs
     */
    public function getEmailChartData()
    {
        try {
            $data = DB::connection('landlord')->table('email_logs')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return $this->return(200, 'Email chart data retrieved successfully', $data->toArray());
        } catch (\Exception $e) {
            return $this->return(500, 'Error retrieving email chart data: ' . $e->getMessage());
        }
    }

    /**
     * Get module statistics
     */
    public function getModuleStats()
    {
        try {
            $modules = [
                'Auth' => [
                    'users' => DB::connection('landlord')->table('users')->count(),
                    'roles' => DB::connection('landlord')->table('roles')->count(),
                    'permissions' => DB::connection('landlord')->table('permissions')->count(),
                ],
                'Utilities' => [
                    'categories' => DB::connection('landlord')->table('categories')->count(),
                    'types' => DB::connection('landlord')->table('types')->count(),
                    'industries' => DB::connection('landlord')->table('industries')->count(),
                    'tags' => DB::connection('landlord')->table('tags')->count(),
                ],
                'Email' => [
                    'templates' => DB::connection('landlord')->table('email_templates')->count(),
                    'campaigns' => DB::connection('landlord')->table('email_campaigns')->count(),
                    'logs' => DB::connection('landlord')->table('email_logs')->count(),
                ],
                'Localization' => [
                    'languages' => DB::connection('landlord')->table('languages')->count(),
                    'translations' => DB::connection('landlord')->table('translations')->count(),
                ],
                'Tenant' => [
                    'tenants' => DB::connection('landlord')->table('tenants')->count(),
                    'customers' => DB::connection('landlord')->table('customers')->count(),
                ],
            ];

            return $this->return(200, 'Module statistics retrieved successfully', $modules);
        } catch (\Exception $e) {
            return $this->return(500, 'Error retrieving module statistics: ' . $e->getMessage());
        }
    }

    /**
     * Calculate growth rate for a given table and date column
     */
    private function calculateGrowthRate($table, $dateColumn)
    {
        try {
            $currentMonth = DB::connection('landlord')->table($table)
                ->whereMonth($dateColumn, now()->month)
                ->whereYear($dateColumn, now()->year)
                ->count();

            $lastMonth = DB::connection('landlord')->table($table)
                ->whereMonth($dateColumn, now()->subMonth()->month)
                ->whereYear($dateColumn, now()->subMonth()->year)
                ->count();

            if ($lastMonth == 0) {
                return $currentMonth > 0 ? 100 : 0;
            }

            return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
