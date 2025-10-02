<?php

namespace Modules\Monitoring\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Modules\Tenant\Entities\Tenant;

class SystemHealthRepository implements SystemHealthInterface
{
    public function getOverview()
    {
        return [
            'total_tenants' => Tenant::count(),
            'active_tenants' => $this->getActiveTenants(),
            'system_uptime' => $this->getSystemUptime(),
            'database_status' => $this->getDatabaseStatus(),
            'queue_health' => $this->getQueueHealth(),
            'last_updated' => now(),
        ];
    }

    public function getTenantHealth($tenantId)
    {
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            return null;
        }

        return [
            'tenant_id' => $tenantId,
            'tenant_name' => $tenant->name,
            'database_status' => $this->getTenantDatabaseStatus($tenant),
            'uptime_status' => $this->getTenantUptimeStatus($tenant),
            'response_time' => $this->getTenantResponseTime($tenant),
            'last_activity' => $this->getTenantLastActivity($tenantId),
            'health_score' => $this->calculateHealthScore($tenant),
        ];
    }

    public function getRealTimeData()
    {
        return [
            'timestamp' => now()->toISOString(),
            'system_load' => $this->getSystemLoad(),
            'memory_usage' => $this->getMemoryUsage(),
            'disk_usage' => $this->getDiskUsage(),
            'active_connections' => $this->getActiveConnections(),
            'queue_size' => $this->getQueueSize(),
        ];
    }

    public function getUptimeStats()
    {
        $tenants = Tenant::all();
        $stats = [];

        foreach ($tenants as $tenant) {
            $stats[] = [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'domain' => $tenant->domain,
                'status' => $this->checkTenantUptime($tenant),
                'response_time' => $this->getTenantResponseTime($tenant),
                'last_checked' => now(),
            ];
        }

        return $stats;
    }

    public function getDatabaseHealth()
    {
        $tenants = Tenant::all();
        $health = [];

        foreach ($tenants as $tenant) {
            $dbHealth = $this->getTenantDatabaseStatus($tenant);
            $health[] = [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'database_name' => $tenant->database,
                'size_mb' => $dbHealth['size_mb'] ?? 0,
                'table_count' => $dbHealth['table_count'] ?? 0,
                'status' => $dbHealth['status'] ?? 'unknown',
                'last_backup' => $dbHealth['last_backup'] ?? null,
            ];
        }

        return $health;
    }

    public function getQueueStats()
    {
        try {
            // Get queue statistics
            $stats = [
                'pending_jobs' => $this->getPendingJobs(),
                'failed_jobs' => $this->getFailedJobs(),
                'completed_jobs_today' => $this->getCompletedJobsToday(),
                'queue_workers' => $this->getActiveWorkers(),
                'average_processing_time' => $this->getAverageProcessingTime(),
            ];

            return $stats;
        } catch (\Exception $e) {
            return [
                'error' => 'Unable to fetch queue stats: ' . $e->getMessage()
            ];
        }
    }

    private function getActiveTenants()
    {
        // Count tenants with recent activity (last 24 hours)
        return Tenant::where('updated_at', '>=', now()->subDay())->count();
    }

    private function getSystemUptime()
    {
        // Get system uptime (placeholder - implement based on your monitoring system)
        return [
            'uptime_seconds' => 86400, // 24 hours
            'uptime_percentage' => 99.9,
            'last_downtime' => null,
        ];
    }

    private function getDatabaseStatus()
    {
        try {
            DB::connection()->getPdo();
            return [
                'status' => 'healthy',
                'connection_count' => $this->getActiveConnections(),
                'query_time' => $this->getAverageQueryTime(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getQueueHealth()
    {
        try {
            $pendingJobs = $this->getPendingJobs();
            $failedJobs = $this->getFailedJobs();
            
            $status = 'healthy';
            if ($failedJobs > 10) {
                $status = 'warning';
            }
            if ($failedJobs > 50 || $pendingJobs > 1000) {
                $status = 'critical';
            }

            return [
                'status' => $status,
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getTenantDatabaseStatus($tenant)
    {
        try {
            $dbName = $tenant->database;
            
            // Get database size
            $sizeQuery = DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$dbName]);

            // Get table count
            $tableQuery = DB::select("
                SELECT COUNT(*) as count 
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$dbName]);

            return [
                'status' => 'healthy',
                'size_mb' => $sizeQuery[0]->size_mb ?? 0,
                'table_count' => $tableQuery[0]->count ?? 0,
                'last_backup' => null, // Implement based on your backup system
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getTenantUptimeStatus($tenant)
    {
        // Check if tenant domain is accessible
        return $this->checkTenantUptime($tenant);
    }

    private function checkTenantUptime($tenant)
    {
        try {
            $url = 'https://' . $tenant->domain;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return [
                'status' => $httpCode >= 200 && $httpCode < 400 ? 'up' : 'down',
                'http_code' => $httpCode,
                'response_received' => !empty($response),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getTenantResponseTime($tenant)
    {
        try {
            $url = 'https://' . $tenant->domain;
            $start = microtime(true);
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            curl_exec($ch);
            curl_close($ch);
            
            $responseTime = (microtime(true) - $start) * 1000; // Convert to milliseconds
            
            return round($responseTime, 2);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getTenantLastActivity($tenantId)
    {
        // This would typically check logs or user sessions
        // For now, return a placeholder
        return now()->subHours(rand(1, 24));
    }

    private function calculateHealthScore($tenant)
    {
        $score = 100;
        
        // Check database status
        $dbStatus = $this->getTenantDatabaseStatus($tenant);
        if ($dbStatus['status'] !== 'healthy') {
            $score -= 30;
        }
        
        // Check uptime status
        $uptimeStatus = $this->getTenantUptimeStatus($tenant);
        if ($uptimeStatus['status'] !== 'up') {
            $score -= 40;
        }
        
        // Check response time
        $responseTime = $this->getTenantResponseTime($tenant);
        if ($responseTime > 2000) { // > 2 seconds
            $score -= 20;
        } elseif ($responseTime > 1000) { // > 1 second
            $score -= 10;
        }
        
        return max(0, $score);
    }

    private function getSystemLoad()
    {
        // Placeholder - implement based on your system monitoring
        return [
            'cpu_usage' => rand(10, 80),
            'load_average' => [
                '1min' => rand(1, 5) / 10,
                '5min' => rand(1, 5) / 10,
                '15min' => rand(1, 5) / 10,
            ],
        ];
    }

    private function getMemoryUsage()
    {
        // Placeholder - implement based on your system monitoring
        return [
            'used_mb' => rand(1000, 4000),
            'total_mb' => 8192,
            'percentage' => rand(20, 80),
        ];
    }

    private function getDiskUsage()
    {
        // Placeholder - implement based on your system monitoring
        return [
            'used_gb' => rand(50, 200),
            'total_gb' => 500,
            'percentage' => rand(10, 40),
        ];
    }

    private function getActiveConnections()
    {
        try {
            $result = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            return $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return rand(10, 50); // Placeholder
        }
    }

    private function getQueueSize()
    {
        return $this->getPendingJobs();
    }

    private function getPendingJobs()
    {
        try {
            return DB::table('jobs')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getFailedJobs()
    {
        try {
            return DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getCompletedJobsToday()
    {
        // This would require a completed_jobs table or log analysis
        // For now, return a placeholder
        return rand(100, 1000);
    }

    private function getActiveWorkers()
    {
        // This would require checking running processes
        // For now, return a placeholder
        return rand(2, 8);
    }

    private function getAverageProcessingTime()
    {
        // This would require job timing data
        // For now, return a placeholder
        return rand(100, 2000); // milliseconds
    }

    private function getAverageQueryTime()
    {
        // This would require query performance monitoring
        // For now, return a placeholder
        return rand(10, 100); // milliseconds
    }
}
