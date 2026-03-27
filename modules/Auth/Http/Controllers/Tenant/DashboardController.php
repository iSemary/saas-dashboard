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
            $overview = $this->getOverviewStats();
            $recentActivity = $this->getRecentActivity();
            
            // Format stats to match frontend expectations
            $stats = [
                'users' => [
                    'total' => $overview['total_users'] ?? 0,
                    'active' => $overview['active_users'] ?? 0,
                ],
                'customers' => [
                    'total' => $this->getCustomersCount(),
                ],
                'subscriptions' => [
                    'total' => $this->getSubscriptionsCount(),
                    'active' => $this->getActiveSubscriptionsCount(),
                ],
                'activity_logs' => [
                    'total' => $this->getActivityLogsCount(),
                ],
                'revenue' => [
                    'current' => $overview['revenue_this_month'] ?? 0,
                    'previous' => $this->getPreviousMonthRevenue(),
                    'growth' => $this->calculateRevenueGrowth(),
                ],
            ];

            // Format recent activity for frontend
            $formattedActivity = array_map(function ($activity) {
                return [
                    'id' => $activity['id'] ?? null,
                    'description' => $activity['message'] ?? $activity['description'] ?? '',
                    'type' => $activity['type'] ?? 'other',
                    'user' => isset($activity['user']) ? ['name' => $activity['user']] : null,
                    'created_at' => isset($activity['time']) ? now()->toISOString() : now()->toISOString(),
                ];
            }, $recentActivity);

            return response()->json([
                'data' => [
                    'stats' => $stats,
                    'recent_activity' => $formattedActivity,
                    'analytics' => $this->getAnalyticsData(),
                    'kpis' => $this->getKPIData(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'Error retrieving dashboard statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analytics data for charts
     */
    public function getAnalytics()
    {
        try {
            return response()->json([
                'data' => $this->getAnalyticsData()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'Error retrieving analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get KPI metrics
     */
    public function getKPIs()
    {
        try {
            return response()->json([
                'data' => $this->getKPIData()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'Error retrieving KPIs: ' . $e->getMessage()
            ], 500);
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
            'total_projects' => $this->getProjectsCount(),
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
        $userName = Auth::user() ? Auth::user()->name : 'System';
        
        // This would typically come from an activity log table
        return [
            [
                'id' => 1,
                'type' => 'user_login',
                'message' => 'New user logged in',
                'user' => $userName,
                'time' => now()->subMinutes(5)->diffForHumans(),
                'icon' => 'fas fa-sign-in-alt',
                'color' => 'success'
            ],
            [
                'id' => 2,
                'type' => 'project_created',
                'message' => 'New project created',
                'user' => $userName,
                'time' => now()->subHours(2)->diffForHumans(),
                'icon' => 'fas fa-folder-plus',
                'color' => 'info'
            ],
            [
                'id' => 3,
                'type' => 'task_completed',
                'message' => 'Task marked as completed',
                'user' => $userName,
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

    /**
     * Get projects count safely
     */
    private function getProjectsCount()
    {
        try {
            return DB::table('projects')->count();
        } catch (\Exception $e) {
            // If projects table doesn't exist, return 0
            return 0;
        }
    }

    /**
     * Get customers count
     */
    private function getCustomersCount()
    {
        try {
            return DB::table('customers')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get subscriptions count
     */
    private function getSubscriptionsCount()
    {
        try {
            return DB::table('subscriptions')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get active subscriptions count
     */
    private function getActiveSubscriptionsCount()
    {
        try {
            return DB::table('subscriptions')->where('status', 'active')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get activity logs count
     */
    private function getActivityLogsCount()
    {
        try {
            return DB::table('audits')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get previous month revenue
     */
    private function getPreviousMonthRevenue()
    {
        try {
            // This would typically come from a payments/transactions table
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calculate revenue growth
     */
    private function calculateRevenueGrowth()
    {
        $current = $this->getMonthlyRevenue();
        $previous = $this->getPreviousMonthRevenue();
        
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Get analytics data for charts
     */
    private function getAnalyticsData()
    {
        // Revenue trend (last 12 months)
        $revenueTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenueTrend[] = [
                'date' => $date->format('Y-m-d'),
                'revenue' => $this->getMonthlyRevenueForDate($date),
            ];
        }

        // User growth (last 12 months)
        $userGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $userGrowth[] = [
                'date' => $date->format('Y-m-d'),
                'users' => $this->getUsersCountForDate($date),
            ];
        }

        // Activity distribution
        $activityDistribution = $this->getActivityDistribution();

        // Performance metrics
        $performanceMetrics = [
            ['metric' => 'Users', 'value' => DB::table('users')->count()],
            ['metric' => 'Customers', 'value' => $this->getCustomersCount()],
            ['metric' => 'Subscriptions', 'value' => $this->getSubscriptionsCount()],
            ['metric' => 'Activity Logs', 'value' => $this->getActivityLogsCount()],
        ];

        return [
            'revenue_trend' => $revenueTrend,
            'user_growth' => $userGrowth,
            'activity_distribution' => $activityDistribution,
            'performance_metrics' => $performanceMetrics,
        ];
    }

    /**
     * Get KPI data
     */
    private function getKPIData()
    {
        $currentRevenue = $this->getMonthlyRevenue();
        $previousRevenue = $this->getPreviousMonthRevenue();
        $revenueGrowth = $this->calculateRevenueGrowth();

        $currentUsers = DB::table('users')->count();
        $previousUsers = $this->getPreviousMonthUsersCount();
        $userGrowth = $previousUsers > 0 
            ? (($currentUsers - $previousUsers) / $previousUsers) * 100 
            : ($currentUsers > 0 ? 100 : 0);

        $currentCustomers = $this->getCustomersCount();
        $previousCustomers = $this->getPreviousMonthCustomersCount();
        $customerGrowth = $previousCustomers > 0 
            ? (($currentCustomers - $previousCustomers) / $previousCustomers) * 100 
            : ($currentCustomers > 0 ? 100 : 0);

        $currentSubscriptions = $this->getActiveSubscriptionsCount();
        $previousSubscriptions = $this->getPreviousMonthSubscriptionsCount();
        $subscriptionGrowth = $previousSubscriptions > 0 
            ? (($currentSubscriptions - $previousSubscriptions) / $previousSubscriptions) * 100 
            : ($currentSubscriptions > 0 ? 100 : 0);

        return [
            'revenue' => [
                'current' => $currentRevenue,
                'previous' => $previousRevenue,
                'growth' => round($revenueGrowth, 2),
            ],
            'users' => [
                'current' => $currentUsers,
                'previous' => $previousUsers,
                'growth' => round($userGrowth, 2),
            ],
            'customers' => [
                'current' => $currentCustomers,
                'previous' => $previousCustomers,
                'growth' => round($customerGrowth, 2),
            ],
            'subscriptions' => [
                'current' => $currentSubscriptions,
                'previous' => $previousSubscriptions,
                'growth' => round($subscriptionGrowth, 2),
            ],
        ];
    }

    /**
     * Get monthly revenue for a specific date
     */
    private function getMonthlyRevenueForDate($date)
    {
        try {
            // This would typically query a payments/transactions table
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get users count for a specific date
     */
    private function getUsersCountForDate($date)
    {
        try {
            return DB::table('users')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get activity distribution
     */
    private function getActivityDistribution()
    {
        try {
            $distribution = DB::table('audits')
                ->select('event', DB::raw('COUNT(*) as count'))
                ->groupBy('event')
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => $item->event,
                        'count' => $item->count,
                    ];
                })
                ->toArray();

            return $distribution;
        } catch (\Exception $e) {
            return [
                ['type' => 'created', 'count' => 0],
                ['type' => 'updated', 'count' => 0],
                ['type' => 'deleted', 'count' => 0],
            ];
        }
    }

    /**
     * Get previous month users count
     */
    private function getPreviousMonthUsersCount()
    {
        try {
            $date = now()->subMonth();
            return DB::table('users')
                ->whereYear('created_at', '<=', $date->year)
                ->where(function ($query) use ($date) {
                    $query->whereMonth('created_at', '<', $date->month)
                          ->orWhere(function ($q) use ($date) {
                              $q->whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month);
                          });
                })
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get previous month customers count
     */
    private function getPreviousMonthCustomersCount()
    {
        try {
            $date = now()->subMonth();
            return DB::table('customers')
                ->whereYear('created_at', '<=', $date->year)
                ->where(function ($query) use ($date) {
                    $query->whereMonth('created_at', '<', $date->month)
                          ->orWhere(function ($q) use ($date) {
                              $q->whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month);
                          });
                })
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get previous month subscriptions count
     */
    private function getPreviousMonthSubscriptionsCount()
    {
        try {
            $date = now()->subMonth();
            return DB::table('subscriptions')
                ->where('status', 'active')
                ->whereYear('created_at', '<=', $date->year)
                ->where(function ($query) use ($date) {
                    $query->whereMonth('created_at', '<', $date->month)
                          ->orWhere(function ($q) use ($date) {
                              $q->whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month);
                          });
                })
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
