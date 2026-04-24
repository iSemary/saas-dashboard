<?php

namespace App\Http\Controllers\Api\CrossDb;

use App\Http\Controllers\Controller;
use App\Services\CrossDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Utilities\Entities\Module;
use Modules\Customer\Entities\Brand;
use Modules\Customer\Entities\Tenant\Brand as TenantBrand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LandlordController extends Controller
{
    protected $crossDbService;

    public function __construct(CrossDatabaseService $crossDbService)
    {
        $this->crossDbService = $crossDbService;
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1'); // Rate limiting
    }

    /**
     * Get all modules
     */
    public function getModules(Request $request): JsonResponse
    {
        try {
            // Verify cross-database request
            if (!$request->hasHeader('X-Cross-DB-Request')) {
                return response()->json(['error' => 'Unauthorized cross-database request'], 403);
            }

            $query = Module::query();

            // Apply filters
            if ($request->has('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('module_key')) {
                $query->where('module_key', $request->module_key);
            }

            $modules = $query->select(['id', 'module_key', 'name', 'description', 'icon', 'status'])
                           ->orderBy('name')
                           ->get();

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

            $module = Module::select(['id', 'module_key', 'name', 'description', 'icon', 'status'])
                          ->findOrFail($id);

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
            
            if (empty($ids)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'count' => 0
                ]);
            }

            $modules = Module::whereIn('id', $ids)
                           ->select(['id', 'module_key', 'name', 'description', 'icon', 'status'])
                           ->get();

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

            $stats = [
                'total_modules' => Module::count(),
                'active_modules' => Module::where('status', 'active')->count(),
                'inactive_modules' => Module::where('status', 'inactive')->count(),
            ];

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
