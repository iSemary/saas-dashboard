<?php

namespace Modules\Auth\Http\Controllers\Landlord;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OwenIt\Auditing\Models\Audit;

class ActivityLogApiController extends ApiController
{
    public function __construct(
        protected ActivityLogService $activityLogService,
        protected Audit $audit,
    ) {
    }

    /**
     * Landlord activity log listing (OwenIt audits), flat array for SPA tables.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'event_type', 'user_id', 'date_from', 'date_to']);
            $perPage = (int) $request->get('per_page', 15);
            if ($perPage < 1) {
                $perPage = 15;
            }
            if ($perPage > 200) {
                $perPage = 200;
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $allowedSort = ['id', 'event', 'created_at', 'auditable_type', 'updated_at'];
            if (! in_array($sortBy, $allowedSort, true)) {
                $sortBy = 'created_at';
            }
            $sortOrder = strtolower((string) $request->get('sort_order', $request->get('sort_direction', 'desc')));
            $sortOrder = $sortOrder === 'asc' ? 'asc' : 'desc';

            $query = $this->audit->newQuery();

            if (! empty($filters['search'])) {
                $term = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $filters['search']) . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('event', 'like', $term)
                        ->orWhere('auditable_type', 'like', $term);
                });
            }
            if (! empty($filters['event_type'])) {
                $query->where('event', $filters['event_type']);
            }
            if (! empty($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }
            if (! empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }
            if (! empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            $page = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

            $rows = collect($page->items())->map(function (Audit $row) {
                $description = trim(implode(' ', array_filter([$row->event, $row->auditable_type])));

                return [
                    'id' => $row->id,
                    'description' => $description !== '' ? $description : (string) $row->event,
                    'subject_type' => $row->auditable_type,
                    'subject_id' => $row->auditable_id,
                    'causer_type' => $row->user_type,
                    'causer_id' => $row->user_id,
                    'created_at' => $row->created_at?->toIso8601String(),
                ];
            })->values()->all();

            return $this->respondWithArray($rows, translate('Activity logs retrieved successfully'));
        } catch (\Exception $e) {
            return $this->return(500, 'Error retrieving activity logs: ' . $e->getMessage());
        }
    }

    /**
     * Get activity log statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'today');
            $stats = $this->activityLogService->getStatistics($period);

            return $this->return(200, translate('Activity log statistics retrieved successfully'), $stats);
        } catch (\Exception $e) {
            return $this->return(500, 'Error retrieving statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get user specific activity logs
     */
    public function getUserActivity(Request $request, $userId): JsonResponse
    {
        try {
            $limit = $request->get('limit', 50);
            $activities = $this->activityLogService->getUserActivity($userId, $limit);

            return $this->return(200, translate('User activity retrieved successfully'), [
                'user_activity' => $activities,
            ]);
        } catch (\Exception $e) {
            return $this->return(500, 'Error retrieving user activity: ' . $e->getMessage());
        }
    }

    /**
     * Get system activity logs
     */
    public function getSystemActivity(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 100);
            $systemActivities = $this->activityLogService->getSystemActivity($limit);

            return $this->return(200, translate('System activity retrieved successfully'), [
                'system_activity' => $systemActivities,
            ]);
        } catch (\Exception $e) {
            return $this->return(500, 'Error retrieving system activity: ' . $e->getMessage());
        }
    }

    /**
     * Clear old activity logs
     */
    public function clearOldLogs(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 90); // Keep last 90 days by default
            $clearedCount = $this->activityLogService->clearOldLogs($days);

            return $this->return(200, translate('Old activity logs cleared successfully'), [
                'cleared_count' => $clearedCount,
                'remaining_days' => $days,
            ]);
        } catch (\Exception $e) {
            return $this->return(500, 'Error clearing old logs: ' . $e->getMessage());
        }
    }

    /**
     * Get activity log trends
     */
    public function getActivityTrends(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '7days');
            $trends = $this->activityLogService->getActivityTrends($period);

            return $this->return(200, translate('Activity trends retrieved successfully'), [
                'trends' => $trends,
            ]);
        } catch (\Exception $e) {
            return $this->return(500, 'Error retrieving activity trends: ' . $e->getMessage());
        }
    }

    /**
     * Get activity summary by event type
     */
    public function getActivitySummary(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '24hours');
            $summary = $this->activityLogService->getActivitySummary($period);

            return $this->return(200, translate('Activity summary retrieved successfully'), [
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            return $this->return(500, 'Error retrieving activity summary: ' . $e->getMessage());
        }
    }

    /**
     * Get audit trail for specific model
     */
    public function getAuditTrail(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'model_type' => 'required|string',
                'model_id' => 'required|integer',
            ]);

            $auditTrail = $this->activityLogService->getAuditTrail(
                $request->model_type,
                $request->model_id
            );

            return $this->return(200, translate('Audit trail retrieved successfully'), [
                'audit_trail' => $auditTrail,
            ]);
        } catch (\Exception $e) {
            return $this->return(500, 'Error retrieving audit trail: ' . $e->getMessage());
        }
    }
}
