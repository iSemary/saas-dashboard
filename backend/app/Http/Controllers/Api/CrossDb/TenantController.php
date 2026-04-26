<?php

namespace App\Http\Controllers\Api\CrossDb;

use App\Http\Controllers\Controller;
use App\Services\CrossDb\TenantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TenantController extends Controller
{
    public function __construct(protected TenantService $tenantService)
    {
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1');
    }

    /**
     * Get all brands for a tenant
     */
    public function getBrands(Request $request): JsonResponse
    {
        try {
            if (!$request->hasHeader('X-Cross-DB-Request')) {
                return response()->json(['error' => translate('auth.unauthorized')], 403);
            }

            $filters = $request->only(['search', 'status', 'created_by']);
            $brands = $this->tenantService->getBrands($filters);

            return response()->json([
                'success' => true,
                'data' => $brands,
                'count' => $brands->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant Brands API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => translate('message.operation_failed'),
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
                return response()->json(['error' => translate('auth.unauthorized')], 403);
            }

            $brand = $this->tenantService->getBrand($id);

            return response()->json([
                'success' => true,
                'data' => $brand
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant Brand API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => translate('message.operation_failed'),
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
                return response()->json(['error' => translate('auth.unauthorized')], 403);
            }

            $modules = $this->tenantService->getBrandModules($brandId);

            return response()->json([
                'success' => true,
                'data' => $modules,
                'count' => $modules->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant Brand Modules API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => translate('message.operation_failed'),
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
                return response()->json(['error' => translate('auth.unauthorized')], 403);
            }

            $moduleIds = $request->input('module_ids', []);

            if (empty($moduleIds)) {
                return response()->json([
                    'success' => false,
                    'error' => translate('message.validation_failed')
                ], 400);
            }

            $assignedCount = $this->tenantService->assignBrandModules($brandId, $moduleIds);

            return response()->json([
                'success' => true,
                'message' => translate('message.action_completed'),
                'assigned_count' => $assignedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant Assign Brand Modules API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => translate('message.operation_failed'),
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
                return response()->json(['error' => translate('auth.unauthorized')], 403);
            }

            $stats = $this->tenantService->getBrandStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant Brand Stats API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => translate('message.operation_failed'),
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
