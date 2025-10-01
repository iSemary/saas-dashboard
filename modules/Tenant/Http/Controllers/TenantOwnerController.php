<?php

namespace Modules\Tenant\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Tenant\Http\Requests\TenantOwnerFormRequest;
use Modules\Tenant\Services\TenantOwnerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TenantOwnerController extends Controller
{
    protected TenantOwnerService $tenantOwnerService;

    public function __construct(TenantOwnerService $tenantOwnerService)
    {
        $this->tenantOwnerService = $tenantOwnerService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'tenant_id', 'role', 'status', 'is_super_admin', 
                'search', 'created_by', 'date_from', 'date_to'
            ]);

            $tenantOwners = $this->tenantOwnerService->getAll($filters, $request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $tenantOwners,
                'message' => 'Tenant owners retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenant owners: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(TenantOwnerFormRequest $request): JsonResponse
    {
        try {
            $tenantOwner = $this->tenantOwnerService->create($request->validated());

            return response()->json([
                'success' => true,
                'data' => $tenantOwner->load(['tenant', 'user', 'creator', 'updater']),
                'message' => 'Tenant owner created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tenant owner: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $tenantOwner = $this->tenantOwnerService->getById((int) $id);

            if (!$tenantOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant owner not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $tenantOwner,
                'message' => 'Tenant owner retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenant owner: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(TenantOwnerFormRequest $request, string $id): JsonResponse
    {
        try {
            $tenantOwner = $this->tenantOwnerService->getById((int) $id);

            if (!$tenantOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant owner not found'
                ], 404);
            }

            $updated = $this->tenantOwnerService->update((int) $id, $request->validated());

            if ($updated) {
                $tenantOwner = $this->tenantOwnerService->getById((int) $id);
                return response()->json([
                    'success' => true,
                    'data' => $tenantOwner,
                    'message' => 'Tenant owner updated successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update tenant owner'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tenant owner: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $tenantOwner = $this->tenantOwnerService->getById((int) $id);

            if (!$tenantOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant owner not found'
                ], 404);
            }

            $deleted = $this->tenantOwnerService->delete((int) $id);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tenant owner deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tenant owner'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tenant owner: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore(string $id): JsonResponse
    {
        try {
            $restored = $this->tenantOwnerService->restore((int) $id);

            if ($restored) {
                $tenantOwner = $this->tenantOwnerService->getById((int) $id);
                return response()->json([
                    'success' => true,
                    'data' => $tenantOwner,
                    'message' => 'Tenant owner restored successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to restore tenant owner'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore tenant owner: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getByTenant(Request $request, string $tenantId): JsonResponse
    {
        try {
            $filters = $request->only([
                'role', 'status', 'is_super_admin', 'search', 
                'created_by', 'date_from', 'date_to'
            ]);

            $tenantOwners = $this->tenantOwnerService->getTenantOwnersForTenant(
                (int) $tenantId, 
                $filters, 
                $request->get('per_page', 15)
            );

            return response()->json([
                'success' => true,
                'data' => $tenantOwners,
                'message' => 'Tenant owners retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenant owners: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSuperAdmins(string $tenantId): JsonResponse
    {
        try {
            $superAdmins = $this->tenantOwnerService->getSuperAdminsForTenant((int) $tenantId);

            return response()->json([
                'success' => true,
                'data' => $superAdmins,
                'message' => 'Super admins retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve super admins: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');
            
            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query is required'
                ], 400);
            }

            $tenantOwners = $this->tenantOwnerService->search($query);

            return response()->json([
                'success' => true,
                'data' => $tenantOwners,
                'message' => 'Search results retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search tenant owners: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = $this->tenantOwnerService->getDashboardStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    public function promoteToSuperAdmin(string $id): JsonResponse
    {
        try {
            $tenantOwner = $this->tenantOwnerService->getById((int) $id);

            if (!$tenantOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant owner not found'
                ], 404);
            }

            $promoted = $this->tenantOwnerService->promoteToSuperAdmin((int) $id);

            if ($promoted) {
                $tenantOwner = $this->tenantOwnerService->getById((int) $id);
                return response()->json([
                    'success' => true,
                    'data' => $tenantOwner,
                    'message' => 'User promoted to super admin successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to promote user to super admin'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to promote user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function demoteFromSuperAdmin(string $id): JsonResponse
    {
        try {
            $tenantOwner = $this->tenantOwnerService->getById((int) $id);

            if (!$tenantOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant owner not found'
                ], 404);
            }

            $demoted = $this->tenantOwnerService->demoteFromSuperAdmin((int) $id);

            if ($demoted) {
                $tenantOwner = $this->tenantOwnerService->getById((int) $id);
                return response()->json([
                    'success' => true,
                    'data' => $tenantOwner,
                    'message' => 'User demoted from super admin successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to demote user from super admin'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to demote user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:active,inactive,suspended'
            ]);

            $tenantOwner = $this->tenantOwnerService->getById((int) $id);

            if (!$tenantOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant owner not found'
                ], 404);
            }

            $updated = $this->tenantOwnerService->update((int) $id, ['status' => $request->status]);

            if ($updated) {
                $tenantOwner = $this->tenantOwnerService->getById((int) $id);
                return response()->json([
                    'success' => true,
                    'data' => $tenantOwner,
                    'message' => 'Status updated successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePermissions(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'permissions' => 'required|array'
            ]);

            $tenantOwner = $this->tenantOwnerService->getById((int) $id);

            if (!$tenantOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant owner not found'
                ], 404);
            }

            $updated = $this->tenantOwnerService->updatePermissions((int) $id, $request->permissions);

            if ($updated) {
                $tenantOwner = $this->tenantOwnerService->getById((int) $id);
                return response()->json([
                    'success' => true,
                    'data' => $tenantOwner,
                    'message' => 'Permissions updated successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions'
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}
