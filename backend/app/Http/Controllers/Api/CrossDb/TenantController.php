<?php

namespace App\Http\Controllers\Api\CrossDb;

use App\Http\Controllers\Controller;
use App\Services\CrossDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Entities\Tenant\Brand;
use Modules\Utilities\Entities\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    protected $crossDbService;

    public function __construct(CrossDatabaseService $crossDbService)
    {
        $this->crossDbService = $crossDbService;
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1'); // Rate limiting
    }

    /**
     * Get all brands for a tenant
     */
    public function getBrands(Request $request): JsonResponse
    {
        try {
            // Verify cross-database request
            if (!$request->hasHeader('X-Cross-DB-Request')) {
                return response()->json(['error' => 'Unauthorized cross-database request'], 403);
            }

            $query = Brand::query();

            // Apply filters
            if ($request->has('search')) {
                $query->search($request->search);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('created_by')) {
                $query->where('created_by', $request->created_by);
            }

            $brands = $query->select(['id', 'name', 'slug', 'description', 'logo', 'status', 'created_at'])
                          ->orderBy('name')
                          ->get();

            // Add modules count for each brand
            $brands->each(function ($brand) {
                $brand->modules_count = DB::table('brand_module')
                    ->where('brand_id', $brand->id)
                    ->count();
            });

            return response()->json([
                'success' => true,
                'data' => $brands,
                'count' => $brands->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant Brands API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch brands',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific brand
     */
    public function getBrand(Request $request, int $id): JsonResponse
    {
        try {
            if (!$request->hasHeader('X-Cross-DB-Request')) {
                return response()->json(['error' => 'Unauthorized cross-database request'], 403);
            }

            $brand = Brand::select(['id', 'name', 'slug', 'description', 'logo', 'status', 'created_at'])
                        ->findOrFail($id);

            // Add modules count
            $brand->modules_count = DB::table('brand_module')
                ->where('brand_id', $brand->id)
                ->count();

            return response()->json([
                'success' => true,
                'data' => $brand
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant Brand API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch brand',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get modules for a specific brand
     */
    public function getBrandModules(Request $request, int $brandId): JsonResponse
    {
        try {
            if (!$request->hasHeader('X-Cross-DB-Request')) {
                return response()->json(['error' => 'Unauthorized cross-database request'], 403);
            }

            // Get module IDs from pivot table
            $moduleIds = DB::table('brand_module')
                ->where('brand_id', $brandId)
                ->pluck('module_id')
                ->toArray();

            if (empty($moduleIds)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'count' => 0
                ]);
            }

            // Get modules from landlord database
            $modules = Module::whereIn('id', $moduleIds)
                           ->select(['id', 'module_key', 'name', 'description', 'icon', 'status'])
                           ->get();

            return response()->json([
                'success' => true,
                'data' => $modules,
                'count' => $modules->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant Brand Modules API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch brand modules',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign modules to a brand
     */
    public function assignBrandModules(Request $request, int $brandId): JsonResponse
    {
        try {
            if (!$request->hasHeader('X-Cross-DB-Request')) {
                return response()->json(['error' => 'Unauthorized cross-database request'], 403);
            }

            $moduleIds = $request->input('module_ids', []);
            
            if (empty($moduleIds)) {
                return response()->json([
                    'success' => false,
                    'error' => 'No module IDs provided'
                ], 400);
            }

            // Verify brand exists
            $brand = Brand::findOrFail($brandId);

            // Clear existing assignments
            DB::table('brand_module')->where('brand_id', $brandId)->delete();
            
            // Insert new assignments
            $assignments = [];
            foreach ($moduleIds as $moduleId) {
                $assignments[] = [
                    'brand_id' => $brandId,
                    'module_id' => $moduleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            DB::table('brand_module')->insert($assignments);

            return response()->json([
                'success' => true,
                'message' => 'Modules assigned successfully',
                'assigned_count' => count($assignments)
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant Assign Brand Modules API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to assign modules',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get brand statistics
     */
    public function getBrandStats(Request $request): JsonResponse
    {
        try {
            if (!$request->hasHeader('X-Cross-DB-Request')) {
                return response()->json(['error' => 'Unauthorized cross-database request'], 403);
            }

            $stats = [
                'total_brands' => Brand::count(),
                'active_brands' => Brand::where('status', 'active')->count(),
                'inactive_brands' => Brand::where('status', 'inactive')->count(),
                'brands_with_modules' => DB::table('brand_module')
                    ->distinct('brand_id')
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant Brand Stats API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch brand statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
