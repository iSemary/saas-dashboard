<?php

namespace Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Customer\Http\Requests\BrandFormRequest;
use Modules\Customer\Services\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
    protected BrandService $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['tenant_id', 'search', 'created_by', 'date_from', 'date_to']);
            $perPage = $request->get('per_page', 15);
            
            $brands = $this->brandService->getAll($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $brands->items(),
                'pagination' => [
                    'current_page' => $brands->currentPage(),
                    'last_page' => $brands->lastPage(),
                    'per_page' => $brands->perPage(),
                    'total' => $brands->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brands.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandFormRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Generate unique slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = $this->brandService->generateUniqueSlug(
                    $data['name'], 
                    $data['tenant_id']
                );
            }

            $brand = $this->brandService->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully.',
                'data' => $brand
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create brand.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $brand = $this->brandService->getById($id);

            if (!$brand) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $brand
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brand.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource by slug.
     */
    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $brand = $this->brandService->getBySlug($slug);

            if (!$brand) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $brand
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brand.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandFormRequest $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Generate unique slug if not provided
            if (empty($data['slug'])) {
                $brand = $this->brandService->getById($id);
                if ($brand) {
                    $data['slug'] = $this->brandService->generateUniqueSlug(
                        $data['name'], 
                        $brand->tenant_id,
                        $id
                    );
                }
            }

            $updated = $this->brandService->update($id, $data);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found.'
                ], 404);
            }

            $brand = $this->brandService->getById($id);

            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully.',
                'data' => $brand
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update brand.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $deleted = $this->brandService->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete brand.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id): JsonResponse
    {
        try {
            $restored = $this->brandService->restore($id);

            if (!$restored) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Brand restored successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore brand.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get brands for a specific tenant.
     */
    public function getByTenant(Request $request, string $tenantId): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'created_by', 'date_from', 'date_to']);
            $perPage = $request->get('per_page', 15);
            
            $brands = $this->brandService->getBrandsForTenant($tenantId, $filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $brands->items(),
                'pagination' => [
                    'current_page' => $brands->currentPage(),
                    'last_page' => $brands->lastPage(),
                    'per_page' => $brands->perPage(),
                    'total' => $brands->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenant brands.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search brands.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');
            
            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query is required.'
                ], 400);
            }

            $brands = $this->brandService->search($query);

            return response()->json([
                'success' => true,
                'data' => $brands
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search brands.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->brandService->getDashboardStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brand statistics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
