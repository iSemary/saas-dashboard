<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\Auth\Services\ActivityLogService;
use Modules\Customer\Services\BrandService;
use Modules\Tenant\Services\TenantService;
use Modules\Subscription\Services\SubscriptionService;

class SuperAdminService
{
    protected ActivityLogService $activityLogService;
    protected BrandService $brandService;
    protected TenantService $tenantService;
    protected SubscriptionService $subscriptionService;

    public function __construct(
        ActivityLogService $activityLogService,
        BrandService $brandService,
        TenantService $tenantService,
        SubscriptionService $subscriptionService
    ) 
    {
        $this->activityLogService = $activityLogService;
        $this->brandService = $brandService;
        $this->tenantService = $tenantService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Get comprehensive dashboard data for super admin
     */
    public function getDashboardData(): array
    {
        return Cache::remember('super_admin_dashboard_data', 300, function () 
        {
            return [
                'overview' => $this->getSystemOverview(),
                'recent_activities' => $this->getRecentActivities(10),
                'system_health' => $this->getSystemHealth(),
                'quick_stats' => $this->getQuickStats(),
                'alerts' => $this->getSystemAlerts(),
                'upcoming_tasks' => $this->getUpcomingTasks(),
                'module_status' => $this->getModuleStatus(),
            ];
        });
    }

    /**
     * Get system overview statistics
     */
    public function getSystemOverview(): array
    {
        $landlordUsers = DB::connection('landlord')->table('users')->count();
        $totalTenants = DB::connection('landlord')->table('tenants')->count();
        $totalBrands = DB::connection('landlord')->table('brands')->count();
        
        // Get tenant user counts
        $tenantUsers = 0;
        $tenants = DB::connection('landlord')->table('tenants')->where('status', 'active')->get();
        
        foreach ($tenants as $tenant) 
        {
            try 
            {
                $tenantDbName = 'saas_' . $tenant->name;
                $tenantUsers += DB::connection('tenant')
                    ->select("SELECT COUNT(*) as count FROM users")[0]->count ?? 0;
            } 
            catch (\Exception $e) 
            {
                // Skip if tenant database not accessible
                continue;
            }
        }

        return [
            'total_users' => $landlordUsers + $tenantUsers,
            'landlord_users' => $landlordUsers,
            'tenant_users' => $tenantUsers,
            'total_tenants' => $totalTenants,
            'total_brands' => $totalBrands,
            'system_uptime' => $this->getSystemUptime(),
            'active_sessions' => $this->getActiveSessions(),
        ];
    }

    /**
     * Get recent system activities
     */
    public function getRecentActivities(int $limit = 20): array
    {
        $activities = $this->activityLogService->getRecentActivities($limit);
        
        return $activities->map(function ($activity) 
        {
            return [
                'id' => $activity->id,
                'type' => $activity->event ?? 'system',
                'description' => $activity->description ?? $this->generateActivityDescription($activity),
                'user_id' => $activity->subject_id ?? null,
                'user_name' => $this->getUserName($activity->subject_id),
                'created_at' => $activity->created_at,
                'ip_address' => $activity->properties['ip'] ?? null,
                'user_agent' => $activity->properties['user_agent'] ?? null,
            ];
        })->toArray();
    }

    /**
     * Get system health status
     */
    public function getSystemHealth(): array
    {
        return [
            'database_status' => $this->getDatabaseStatus(),
            'disk_usage' => $this->getDiskUsage(),
            'memory_usage' => $this->getMemoryUsage(),
            'system_load' => $this->getSystemLoad(),
            'last_backup' => $this->getLastBackupTime(),
            'ssl_status' => $this->getSSLStatus(),
            'queue_status' => $this->getQueueStatus(),
        ];
    }

    /**
     * Get quick statistics
     */
    public function getQuickStats(): array
    {
        return [
            'new_users_today' => DB::connection('landlord')->table('users')
                ->whereDate('created_at', today())->count(),
            'failed_login_attempts' => DB::connection('landlord')->table('login_attempts')
                ->where('created_at', '>=', now()->subHour())->count(),
            'active_subscriptions' => $this->brandModuleService->getActiveSubscriptionsCount(),
            'pending_tickets' => 0, // Placeholder - implement if ticket system exists
            'system_alerts' => $this->getSystemAlertsCount(),
        ];
    }

    /**
     * Get system alerts
     */
    public function getSystemAlerts(): array
    {
        $alerts = [];
        
        // Check disk usage
        $diskUsage = $this->getDiskUsage();
        if ($diskUsage['percentage'] > 90) 
        {
            $alerts[] = [
                'type' => 'critical',
                'title' => translate('disk_space_critical'),
                'message' => translate('disk_space_critical_message'),
                'action_required' => true,
            ];
        }
        
        // Check database connections
        $dbStatus = $this->getDatabaseStatus();
        if (!$dbStatus['landlord_connected']) 
        {
            $alerts[] = [
                'type' => 'critical',
                'title' => translate('database_connection_failed'),
                'message' => translate('landlord_database_not_accessible'),
                'action_required' => true,
            ];
        }
        
        // Check for failed login attempts
        $failedAttempts = DB::connection('landlord')->table('login_attempts')
            ->where('created_at', '>=', now()->subHour())
            ->where('ip', 'like', '%failed%') // Assuming failed attempts are tracked differently
            ->count();
            
        if ($failedAttempts > 50) 
        {
            $alerts[] = [
                'type' => 'warning',
                'title' => translate('suspicious_login_activity'),
                'message' => translate('high_number_of_failed_logins'),
                'action_required' => false,
            ];
        }

        return $alerts;
    }

    /**
     * Get upcoming tasks
     */
    public function getUpcomingTasks(): array
    {
        return [
            [
                'id' => 1,
                'title' => translate('perform_system_backup'),
                'description' => translate('daily_backup_of_databases'),
                'due_date' => now()->addHours(6),
                'priority' => 'high',
                'completed' => false,
            ],
            [
                'id' => 2,
                'title' => translate('review_login_attempts'),
                'description' => translate('review_hourly_login_reports'),
                'due_date' => now()->addMinutes(30),
                'priority' => 'medium',
                'completed' => false,
            ],
            // Add more tasks as needed
        ];
    }

    /**
     * Get module status
     */
    public function getModuleStatus(): array
    {
        $modules = [
            'auth' => ['status' => 'active', 'version' => '1.0.0'],
            'customer' => ['status' => 'active', 'version' => '1.0.0'],
            'tenant' => ['status' => 'active', 'version' => '1.0.0'],
            'subscription' => ['status' => 'active', 'version' => '1.0.0'],
            'brand_module' => ['status' => 'active', 'version' => '1.0.0'],
        ];

        return $modules;
    }

    /**
     * Database status check
     */
    private function getDatabaseStatus(): array
    {
        $status = ['landlord_connected' => false, 'tenants_connected' => 0];
        
        try 
        {
            DB::connection('landlord')->table('users')->first();
            $status['landlord_connected'] = true;
        } 
        catch (\Exception $e) 
        {
            $status['landlord_connected'] = false;
            $status['landlord_error'] = $e->getMessage();
        }

        // Check tenant databases
        $tenants = DB::connection('landlord')->table('tenants')->get();
        foreach ($tenants as $tenant) 
        {
            try 
            {
                config(['database.connections.tenant.database' => 'saas_' . $tenant->name]);
                DB::connection('tenant')->table('users')->first();
                $status['tenants_connected']++;
            } 
            catch (\Exception $e) 
            {
                // Skip failed tenant connections
                continue;
            }
        }

        return $status;
    }

    /**
     * Get disk usage
     */
    private function getDiskUsage(): array
    {
        $totalSpace = disk_total_space(storage_path());
        $freeSpace = disk_free_space(storage_path());
        $usedSpace = $totalSpace - $freeSpace;
        
        return [
            'total' => $totalSpace,
            'free' => $freeSpace,
            'used' => $usedSpace,
            'percentage' => ($usedSpace / $totalSpace) * 100,
        ];
    }

    /**
     * Get memory usage
     */
    private function getMemoryUsage(): array
    {
        $memoryUsage = memory_get_usage(true);
        $peakMemoryUsage = memory_get_peak_usage(true);
        
        return [
            'current' => $memoryUsage,
            'peak' => $peakMemoryUsage,
            'current_mb' => round($memoryUsage / 1024 / 1024, 2),
            'peak_mb' => round($peakMemoryUsage / 1024 / 1024, 2),
        ];
    }

    /**
     * Get system load
     */
    private function getSystemLoad(): array
    {
        $loadAvg = sys_getloadavg();
        
        return [
            '1min' => $loadAvg[0] ?? 0,
            '5min' => $loadAvg[1] ?? 0,
            '15min' => $loadAvg[2] ?? 0,
        ];
    }

    /**
     * Get system uptime
     */
    private function getSystemUptime(): string
    {
        try 
        {
            $uptime = file_get_contents('/proc/uptime');
            $seconds = floatval(explode(' ', $uptime)[0]);
            
            $days = floor($seconds / 86400);
            $hours = floor(($seconds % 86400) / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            
            return sprintf('%d days, %d hours, %d minutes', $days, $hours, $minutes);
        } 
        catch (\Exception $e) 
        {
            return translate('uptime_unavailable');
        }
    }

    /**
     * Get active sessions count
     */
    private function getActiveSessions(): int
    {
        try 
        {
            return DB::connection('landlord')->table('sessions')->count();
        } 
        catch (\Exception $e) 
        {
            return 0;
        }
    }

    /**
     * Get last backup time
     */
    private function getLastBackupTime(): string|null
    {
        // Implement backup tracking logic
        return null;
    }

    /**
     * Get SSL status
     */
    private function getSSLStatus(): bool
    {
        return request()->isSecure();
    }

    /**
     * Get queue status
     */
    private function getQueueStatus(): array
    {
        try 
        {
            return [
                'pending_jobs' => DB::connection('landlord')->table('jobs')->count(),
                'failed_jobs' => DB::connection('landlord')->table('failed_jobs')->count(),
            ];
        } 
        catch (\Exception $e) 
        {
            return ['pending_jobs' => 0, 'failed_jobs' => 0];
        }
    }

    /**
     * Get system alerts count
     */
    private function getSystemAlertsCount(): int
    {
        return count($this->getSystemAlerts());
    }

    /**
     * Generate activity description
     */
    private function generateActivityDescription($activity): string
    {
        $subjectType = class_basename($activity->subject_type ?? '');
        $event = $activity->event ?? 'unknown';
        
        return translate("activity_{$subjectType}_{$event}", []);
    }

    /**
     * Get user name by ID
     */
    private function getUserName($userId): string|null
    {
        if (!$userId) 
        {
            return null;
        }
        
        try 
        {
            return DB::connection('landlord')->table('users')->where('id', $userId)->value('name');
        } 
        catch (\Exception $e) 
        {
            return null;
        }
    }
}
