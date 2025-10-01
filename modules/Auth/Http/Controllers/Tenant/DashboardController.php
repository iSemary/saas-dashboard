<?php

namespace Modules\Auth\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Tenant\Helper\TenantHelper;
use Modules\Tenant\Entities\Tenant;

class DashboardController extends Controller
{
    /**
     * Show the tenant dashboard
     */
    public function index()
    {
        $subdomain = TenantHelper::getSubDomain();
        $tenant = Tenant::where('domain', $subdomain)->first();
        $user = Auth::user();
        
        // Get dashboard statistics
        $stats = [
            'overview' => $this->getOverviewStats(),
            'recent_activity' => $this->getRecentActivity(),
            'quick_actions' => $this->getQuickActions(),
            'notifications' => $this->getNotifications(),
        ];
        
        return view('tenant.dashboard.index', compact('tenant', 'user', 'stats'));
    }

    /**
     * Get dashboard statistics for the tenant
     */
    public function getStats()
    {
        try {
            $stats = [
                'overview' => $this->getOverviewStats(),
                'recent_activity' => $this->getRecentActivity(),
                'quick_actions' => $this->getQuickActions(),
                'notifications' => $this->getNotifications(),
            ];

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Dashboard statistics retrieved successfully',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'Error retrieving dashboard statistics: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats()
    {
        $subdomain = TenantHelper::getSubDomain();
        $tenant = Tenant::where('domain', $subdomain)->first();
        
        return [
            'total_users' => DB::table('users')->count(),
            'active_users' => DB::table('users')->whereNotNull('email_verified_at')->count(),
            'total_projects' => DB::table('projects')->count() ?? 0,
            'total_tasks' => 0, // Placeholder - tasks table doesn't exist yet
            'revenue_this_month' => $this->getMonthlyRevenue(),
            'growth_rate' => $this->calculateGrowthRate(),
        ];
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity()
    {
        // This would typically come from an activity log table
        return [
            [
                'id' => 1,
                'type' => 'user_login',
                'message' => 'New user logged in',
                'user' => Auth::user()->name,
                'time' => now()->subMinutes(5)->diffForHumans(),
                'icon' => 'fas fa-sign-in-alt',
                'color' => 'success'
            ],
            [
                'id' => 2,
                'type' => 'project_created',
                'message' => 'New project created',
                'user' => Auth::user()->name,
                'time' => now()->subHours(2)->diffForHumans(),
                'icon' => 'fas fa-folder-plus',
                'color' => 'info'
            ],
            [
                'id' => 3,
                'type' => 'task_completed',
                'message' => 'Task marked as completed',
                'user' => Auth::user()->name,
                'time' => now()->subDays(1)->diffForHumans(),
                'icon' => 'fas fa-check-circle',
                'color' => 'primary'
            ]
        ];
    }

    /**
     * Get quick actions
     */
    private function getQuickActions()
    {
        return [
            [
                'title' => 'Create Project',
                'description' => 'Start a new project',
                'icon' => 'fas fa-plus-circle',
                'url' => '#',
                'color' => 'primary'
            ],
            [
                'title' => 'Add User',
                'description' => 'Invite team members',
                'icon' => 'fas fa-user-plus',
                'url' => '#',
                'color' => 'success'
            ],
            [
                'title' => 'View Reports',
                'description' => 'Analytics & insights',
                'icon' => 'fas fa-chart-bar',
                'url' => '#',
                'color' => 'info'
            ],
            [
                'title' => 'Settings',
                'description' => 'Configure your workspace',
                'icon' => 'fas fa-cog',
                'url' => '#',
                'color' => 'warning'
            ]
        ];
    }

    /**
     * Get notifications
     */
    private function getNotifications()
    {
        return [
            [
                'id' => 1,
                'title' => 'Welcome to your dashboard!',
                'message' => 'Get started by exploring the features and customizing your workspace.',
                'type' => 'info',
                'read' => false,
                'time' => now()->subHours(1)->diffForHumans()
            ],
            [
                'id' => 2,
                'title' => 'System Update',
                'message' => 'New features have been added to improve your experience.',
                'type' => 'success',
                'read' => false,
                'time' => now()->subDays(1)->diffForHumans()
            ]
        ];
    }

    /**
     * Get monthly revenue (placeholder)
     */
    private function getMonthlyRevenue()
    {
        // This would typically come from a payments/transactions table
        return 0;
    }

    /**
     * Calculate growth rate (placeholder)
     */
    private function calculateGrowthRate()
    {
        // This would typically calculate growth based on historical data
        return 12.5;
    }
}
