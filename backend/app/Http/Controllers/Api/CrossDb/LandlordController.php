<?php

namespace App\Http\Controllers\Api\CrossDb;

use App\Http\Controllers\Controller;
use App\Services\CrossDb\LandlordService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LandlordController extends Controller
{
    public function __construct(protected LandlordService $landlordService)
    {
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1');
    }

    /**
     * Get all modules
     */
    public function getModules(Request $request): JsonResponse
    {
        try {
            if (!$request->hasHeader('X-Cross-DB-Request')) {
                return response()->json(['error' => 'Unauthorized cross-database request'], 403);
            }

            $filters = $request->only(['search', 'status', 'module_key']);
            $modules = $this->landlordService->getModules($filters);

            return response()->json([
                'success' => true,
                'data' => $modules,
                'count' => $modules->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Landlord Modules API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch modules',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific module
     */
    public function getModule(Request $request, int $id): JsonResponse
    {
        try {
            if (!$request->hasHeader('X-Cross-DB-Request')) {
                return response()->json(['error' => 'Unauthorized cross-database request'], 403);
            }

            $module = $this->landlordService->getModule($id);

            return response()->json([
                'success' => true,
                'data' => $module
            ]);

        } catch (\Exception $e) {
            \Log::error('Landlord Module API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch module',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get modules by IDs
     */
    public function getModulesByIds(Request $request): JsonResponse
    {
        try {
            if (!$request->hasHeader('X-Cross-DB-Request')) {
                return response()->json(['error' => 'Unauthorized cross-database request'], 403);
            }

            $ids = $request->input('ids', []);
            $modules = $this->landlordService->getModulesByIds($ids);

            return response()->json([
                'success' => true,
                'data' => $modules,
                'count' => $modules->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Landlord Modules by IDs API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch modules',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get module statistics
     */
    public function getModuleStats(Request $request): JsonResponse
    {
        try {
            if (!$request->hasHeader('X-Cross-DB-Request')) {
                return response()->json(['error' => 'Unauthorized cross-database request'], 403);
            }

            $stats = $this->landlordService->getModuleStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Landlord Module Stats API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch module statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
