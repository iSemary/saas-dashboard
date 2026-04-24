<?php

namespace Modules\Auth\Http\Controllers\Landlord;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActivityLogApiController extends ApiController
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Get paginated activity logs
     */
    public function index(Request $request): JsonResponse
    {
        try 
        {
            $filters = $request->only(['search', 'event_type', 'user_id', 'date_from', 'date_to']);
            $perPage = $request->get('per_page', 15);
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $logs = $this->activityLogService->getPaginatedLogs($filters, $perPage, [$sortBy => $sortOrder]);

            return $this->return(200, translate('Activity logs retrieved successfully'), [
                'logs' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                    'from' => $logs->firstItem(),
                    'to' => $logs->lastItem(),
                ],
                'filters' => $filters,
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving activity logs: ' . $e->getMessage());
        }
    }

    /**
     * Get activity log statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        try 
        {
            $period = $request->get('period', 'today');
            $stats = $this->activityLogService->getStatistics($period);
            
            return $this->return(200, translate('Activity log statistics retrieved successfully'), $stats);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get user specific activity logs
     */
    public function getUserActivity(Request $request, $userId): JsonResponse
    {
        try 
        {
            $limit = $request->get('limit', 50);
            $activities = $this->activityLogService->getUserActivity($userId, $limit);
            
            return $this->return(200, translate('User activity retrieved successfully'), [
                'user_activity' => $activities
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving user activity: ' . $e->getMessage());
        }
    }

    /**
     * Get system activity logs
     */
    public function getSystemActivity(Request $request): JsonResponse
    {
        try 
        {
            $limit = $request->get('limit', 100);
            $systemActivities = $this->activityLogService->getSystemActivity($limit);
            
            return $this->return(200, translate('System activity retrieved successfully'), [
                'system_activity' => $systemActivities
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving system activity: ' . $e->getMessage());
        }
    }

    /**
     * Clear old activity logs
     */
    public function clearOldLogs(Request $request): JsonResponse
    {
        try 
        {
            $days = $request->get('days', 90); // Keep last 90 days by default
            $clearedCount = $this->activityLogService->clearOldLogs($days);
            
            return $this->return(200, translate('Old activity logs cleared successfully'), [
                'cleared_count' => $clearedCount,
                'remaining_days' => $days
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error clearing old logs: ' . $e->getMessage());
        }
    }

    /**
     * Get activity log trends
     */
    public function getActivityTrends(Request $request): JsonResponse
    {
        try 
        {
            $period = $request->get('period', '7days');
            $trends = $this->activityLogService->getActivityTrends($period);
            
            return $this->return(200, translate('Activity trends retrieved successfully'), [
                'trends' => $trends
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving activity trends: ' . $e->getMessage());
        }
    }

    /**
     * Get activity summary by event type
     */
    public function getActivitySummary(Request $request): JsonResponse
    {
        try 
        {
            $period = $request->get('period', '24hours');
            $summary = $this->activityLogService->getActivitySummary($period);
            
            return $this->return(200, translate('Activity summary retrieved successfully'), [
                'summary' => $summary
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving activity summary: ' . $e->getMessage());
        }
    }

    /**
     * Get audit trail for specific model
     */
    public function getAuditTrail(Request $request): JsonResponse
    {
        try 
        {
            $request->validate([
                'model_type' => 'required|string',
                'model_id' => 'required|integer'
            ]);

            $auditTrail = $this->activityLogService->getAuditTrail(
                $request->model_type,
                $request->model_id
            );
            
            return $this->return(200, translate('Audit trail retrieved successfully'), [
                'audit_trail' => $auditTrail
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving audit trail: ' . $e->getMessage());
        }
    }
}
