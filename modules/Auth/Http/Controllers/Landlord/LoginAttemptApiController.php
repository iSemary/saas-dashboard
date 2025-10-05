<?php

namespace Modules\Auth\Http\Controllers\Landlord;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\LoginAttemptService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LoginAttemptApiController extends ApiController
{
    protected LoginAttemptService $loginAttemptService;

    public function __construct(LoginAttemptService $loginAttemptService)
    {
        $this->loginAttemptService = $loginAttemptService;
    }

    /**
     * Get paginated login attempts
     */
    public function index(Request $request): JsonResponse
    {
        try 
        {
            $filters = $request->only(['search', 'status', 'ip', 'date_from', 'date_to']);
            $perPage = $request->get('per_page', 15);
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $attempts = $this->loginAttemptService->getPaginatedAttempts($filters, $perPage, [$sortBy => $sortOrder]);

            return $this->return(200, translate('Login attempts retrieved successfully'), [
                'attempts' => $attempts->items(),
                'pagination' => [
                    'current_page' => $attempts->currentPage(),
                    'last_page' => $attempts->lastPage(),
                    'per_page' => $attempts->perPage(),
                    'total' => $attempts->total(),
                    'from' => $attempts->firstItem(),
                    'to' => $attempts->lastItem(),
                ],
                'filters' => $filters,
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving login attempts: ' . $e->getMessage());
        }
    }

    /**
     * Get login attempt statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        try 
        {
            $period = $request->get('period', 'today'); // today, week, month, year
            $stats = $this->loginAttemptService->getStatistics($period);
            
            return $this->return(200, translate('Login attempt statistics retrieved successfully'), $stats);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get failed login attempts
     */
    public function getFailedAttempts(Request $request): JsonResponse
    {
        try 
        {
            $limit = $request->get('limit', 50);
            $failedAttempts = $this->loginAttemptService->getFailedAttempts($limit);
            
            return $this->return(200, translate('Failed login attempts retrieved successfully'), [
                'failed_attempts' => $failedAttempts
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving failed attempts: ' . $e->getMessage());
        }
    }

    /**
     * Get recent login activity
     */
    public function getRecentActivity(Request $request): JsonResponse
    {
        try 
        {
            $limit = $request->get('limit', 20);
            $activities = $this->loginAttemptService->getRecentActivity($limit);
            
            return $this->return(200, translate('Recent login activity retrieved successfully'), [
                'activities' => $activities
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving recent activity: ' . $e->getMessage());
        }
    }

    /**
     * Block IP address
     */
    public function blockIP(Request $request, string $ip): JsonResponse
    {
        try 
        {
            $this->loginAttemptService->blockIP($ip, $request->get('reason', 'Excessive failed login attempts'));
            
            return $this->return(200, translate('IP address blocked successfully'), [
                'ip' => $ip,
                'status' => 'blocked'
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error blocking IP: ' . $e->getMessage());
        }
    }

    /**
     * Unblock IP address
     */
    public function unblockIP(Request $request, string $ip): JsonResponse
    {
        try 
        {
            $this->loginAttemptService->unblockIP($ip);
            
            return $this->return(200, translate('IP address unblocked successfully'), [
                'ip' => $ip,
                'status' => 'unblocked'
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error unblocking IP: ' . $e->getMessage());
        }
    }

    /**
     * Get blocked IPs
     */
    public function getBlockedIPs(Request $request): JsonResponse
    {
        try 
        {
            $blockedIPs = $this->loginAttemptService->getBlockedIPs();
            
            return $this->return(200, translate('Blocked IPs retrieved successfully'), [
                'blocked_ips' => $blockedIPs
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving blocked IPs: ' . $e->getMessage());
        }
    }

    /**
     * Get security alerts
     */
    public function getSecurityAlerts(Request $request): JsonResponse
    {
        try 
        {
            $alerts = $this->loginAttemptService->getSecurityAlerts();
            
            return $this->return(200, translate('Security alerts retrieved successfully'), [
                'alerts' => $alerts
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving security alerts: ' . $e->getMessage());
        }
    }

    /**
     * Get login trends over time
     */
    public function getLoginTrends(Request $request): JsonResponse
    {
        try 
        {
            $period = $request->get('period', '7days');
            $trends = $this->loginAttemptService->getLoginTrends($period);
            
            return $this->return(200, translate('Login trends retrieved successfully'), [
                'trends' => $trends
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving login trends: ' . $e->getMessage());
        }
    }

    /**
     * Get suspicious activity
     */
    public function getSuspiciousActivity(Request $request): JsonResponse
    {
        try 
        {
            $limit = $request->get('limit', 20);
            $suspiciousActivity = $this->loginAttemptService->getSuspiciousActivity($limit);
            
            return $this->return(200, translate('Suspicious activity retrieved successfully'), [
                'suspicious_activity' => $suspiciousActivity
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving suspicious activity: ' . $e->getMessage());
        }
    }
}
