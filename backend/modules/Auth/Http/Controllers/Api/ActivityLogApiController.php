<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Auth\Services\ActivityLogService;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $userId = $request->get('user_id');
            $eventType = $request->get('event_type');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $search = $request->get('search');

            $query = Audit::query();

            // Filter by user if provided
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                // Default to current user's activities
                $query->where('user_id', auth()->id());
            }

            // Filter by event type
            if ($eventType) {
                $query->where('event', $eventType);
            }

            // Filter by date range
            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            // Search
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('auditable_type', 'like', "%{$search}%")
                      ->orWhere('event', 'like', "%{$search}%")
                      ->orWhere('url', 'like', "%{$search}%");
                });
            }

            $logs = $query->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $formattedLogs = $logs->getCollection()->map(function ($log) {
                return [
                    'id' => $log->id,
                    'event' => $log->event,
                    'type' => $log->auditable_type,
                    'type_id' => $log->auditable_id,
                    'user' => $log->user ? [
                        'id' => $log->user->id,
                        'name' => $log->user->name,
                        'email' => $log->user->email,
                    ] : null,
                    'old_values' => $log->old_values,
                    'new_values' => $log->new_values,
                    'url' => $log->url,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'created_at' => $log->created_at->toISOString(),
                ];
            });

            return response()->json([
                'data' => $formattedLogs,
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                    'from' => $logs->firstItem(),
                    'to' => $logs->lastItem(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single activity log details
     */
    public function show($id): JsonResponse
    {
        try {
            $log = Audit::findOrFail($id);

            // Check if user has access to this log
            if ($log->user_id !== auth()->id() && !auth()->user()->hasPermissionTo('view-all-activity-logs')) {
                return response()->json([
                    'message' => translate('auth.unauthorized')
                ], 403);
            }

            return response()->json([
                'data' => [
                    'id' => $log->id,
                    'event' => $log->event,
                    'type' => $log->auditable_type,
                    'type_id' => $log->auditable_id,
                    'user' => $log->user ? [
                        'id' => $log->user->id,
                        'name' => $log->user->name,
                        'email' => $log->user->email,
                    ] : null,
                    'old_values' => $log->old_values,
                    'new_values' => $log->new_values,
                    'url' => $log->url,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'tags' => $log->tags,
                    'created_at' => $log->created_at->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export activity logs
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $format = $request->get('format', 'csv');
            $userId = $request->get('user_id');
            $eventType = $request->get('event_type');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            $query = Audit::query();

            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('user_id', auth()->id());
            }

            if ($eventType) {
                $query->where('event', $eventType);
            }

            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            $logs = $query->orderBy('created_at', 'desc')->get();

            // For now, return the data - actual file generation would be implemented
            return response()->json([
                'message' => translate('message.operation_failed'),
                'format' => $format,
                'count' => $logs->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed') . $e->getMessage()
            ], 500);
        }
    }
}
