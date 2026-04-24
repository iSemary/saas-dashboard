<?php

namespace Modules\Auth\Services;

use Modules\Auth\Entities\LoginAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class LoginAttemptService
{
    /**
     * Get paginated login attempts
     */
    public function getPaginatedAttempts(array $filters = [], int $perPage = 15, array $orderBy = []): LengthAwarePaginator
    {
        $query = LoginAttempt::with('user');

        // Apply filters
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['ip'])) {
            $query->where('ip', $filters['ip']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Apply ordering
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        if (empty($orderBy)) {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get login attempt statistics
     */
    public function getStats(string $period = 'today'): array
    {
        $cacheKey = "login_attempts_stats_{$period}";
        
        return Cache::remember($cacheKey, 300, function () use ($period) {
            $startDate = $this->getPeriodStartDate($period);
            
            $stats = [
                'total_attempts' => LoginAttempt::where('created_at', '>=', $startDate)->count(),
                'unique_ips' => LoginAttempt::where('created_at', '>=', $startDate)
                    ->distinct('ip')->count('ip'),
                'failed_attempts' => LoginAttempt::where('created_at', '>=', $startDate)
                    ->where('created_at', '>=', $startDate)->count(),
                'most_common_ip' => LoginAttempt::where('created_at', '>=', $startDate)
                    ->selectRaw('ip, count(*) as count')
                    ->groupBy('ip')
                    ->orderBy('count', 'desc')
                    ->first(),
                'attempts_by_hour' => $this->getAttemptsByHour($startDate),
            ];

            return $stats;
        });
    }

    /**
     * Get failed attempts
     */
    public function getFailedAttempts(int $limit = 50): array
    {
        return LoginAttempt::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'user_id' => $attempt->user_id,
                    'user_name' => $attempt->user->name ?? 'Unknown',
                    'ip' => $attempt->ip,
                    'user_agent' => $attempt->agent,
                    'created_at' => $attempt->created_at,
                ];
            })
            ->toArray();
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity(int $limit = 20): array
    {
        return LoginAttempt::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'user_id' => $attempt->user_id,
                    'user_name' => $attempt->user->name ?? 'Unknown',
                    'ip' => $attempt->ip,
                    'user_agent' => $attempt->agent,
                    'created_at' => $attempt->created_at,
                    'status' => 'unknown', // Would need to determine success/failure
                ];
            })
            ->toArray();
    }

    /**
     * Block IP address
     */
    public function blockIP(string $ip, string $reason = 'Excessive failed login attempts'): bool
    {
        try {
            DB::table('blocked_ips')->insert([
                'ip' => $ip,
                'reason' => $reason,
                'blocked_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Unblock IP address
     */
    public function unblockIP(string $ip): bool
    {
        try {
            DB::table('blocked_ips')->where('ip', $ip)->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get blocked IPs
     */
    public function getBlockedIPs(): array
    {
        try {
            return DB::table('blocked_ips')
                ->orderBy('blocked_at', 'desc')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get security alerts
     */
    public function getSecurityAlerts(): array
    {
        $alerts = [];
        
        // Check for excessive failed attempts
        $failedAttempts = LoginAttempt::where('created_at', '>=', now()->subHour())->count();
        
        if ($failedAttempts > 20) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'High Failed Login Attempts',
                'message' => "{$failedAttempts} failed attempts in the last hour",
                'severity' => 'medium',
            ];
        }

        // Check for suspicious IPs
        $suspiciousIPs = LoginAttempt::selectRaw('ip, count(*) as count')
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('ip')
            ->having('count', '>', 5)
            ->get();

        foreach ($suspiciousIPs as $ip) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Suspicious IP Activity',
                'message' => "IP {$ip->ip} has {$ip->count} attempts in the last hour",
                'severity' => 'high',
                'ip' => $ip->ip,
            ];
        }

        return $alerts;
    }

    /**
     * Get login trends
     */
    public function getLoginTrends(string $period = '7days'): array
    {
        $startDate = $this->getPeriodStartDate($period);
        
        return LoginAttempt::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($trend) {
                return [
                    'date' => $trend->date,
                    'count' => $trend->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get suspicious activity
     */
    public function getSuspiciousActivity(int $limit = 20): array
    {
        return LoginAttempt::selectRaw('ip, COUNT(*) as attempts')
            ->where('created_at', '>=', now()->subDay())
            ->groupBy('ip')
            ->having('attempts', '>', 3)
            ->orderBy('attempts', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get period start date
     */
    private function getPeriodStartDate(string $period): string
    {
        switch ($period) {
            case 'today':
                return now()->startOfDay()->toDateTimeString();
            case 'week':
                return now()->subWeek()->toDateTimeString();
            case 'month':
                return now()->subMonth()->toDateTimeString();
            case 'year':
                return now()->subYear()->toDateTimeString();
            default:
                return now()->startOfDay()->toDateTimeString();
        }
    }

    /**
     * Get attempts by hour
     */
    private function getAttemptsByHour(\DateTime $startDate): array
    {
        return LoginAttempt::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get()
            ->map(function ($attempt) {
                return [
                    'hour' => $attempt->hour,
                    'count' => $attempt->count,
                ];
            })
            ->toArray();
    }
}
