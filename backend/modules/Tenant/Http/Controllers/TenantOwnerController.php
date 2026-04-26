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

    public function index(Request $request)
    {
        try {
            $filters = $request->only([
                'tenant_id', 'role', 'status', 'is_super_admin', 
                'search', 'created_by', 'date_from', 'date_to'
            ]);

            // If it's an AJAX request, return JSON for DataTable
            if ($request->ajax()) {
                $tenantOwners = $this->tenantOwnerService->getAll($filters, $request->get('per_page', 15));

                return response()->json([
                    'success' => true,
                    'data' => $tenantOwners,
                    'message' => translate('message.action_completed')
                ]);
            }

            // For regular page view, return the view with data
            $tenants = \Modules\Tenant\Entities\Tenant::all();
            $users = \Modules\Auth\Entities\User::all();

            return view('landlord.tenant-owners.index', compact('tenants', 'users'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => translate('message.operation_failed') . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to retrieve tenant owners: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $tenants = \Modules\Tenant\Entities\Tenant::all();
        $users = \Modules\Auth\Entities\User::all();
        
        return view('landlord.tenant-owners.create', compact('tenants', 'users'));
    }

    public function edit(string $id)
    {
        $tenantOwner = $this->tenantOwnerService->getById((int) $id);
        
        if (!$tenantOwner) {
            return redirect()->back()->with('error', 'Tenant owner not found');
        }
        
        $tenants = \Modules\Tenant\Entities\Tenant::all();
        $users = \Modules\Auth\Entities\User::all();
        
        return view('landlord.tenant-owners.edit', compact('tenantOwner', 'tenants', 'users'));
    }

    public function store(TenantOwnerFormRequest $request)
    {
        try {
            $tenantOwner = $this->tenantOwnerService->create($request->validated());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $tenantOwner->load(['tenant', 'user', 'creator', 'updater']),
                    'message' => translate('message.created_successfully')
                ], 201);
            }
            
            return redirect()->back()->with('success', 'Tenant owner created successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => translate('message.operation_failed') . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to create tenant owner: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $tenantOwner = $this->tenantOwnerService->getById((int) $id);

            if (!$tenantOwner) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => translate('message.resource_not_found')
                    ], 404);
                }
                return redirect()->back()->with('error', 'Tenant owner not found');
            }

            // If it's an AJAX request, return JSON
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $tenantOwner,
                    'message' => translate('message.action_completed')
                ]);
            }

            // For modal view, return the view
            return view('landlord.tenant-owners.show', compact('tenantOwner'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => translate('message.operation_failed') . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to retrieve tenant owner: ' . $e->getMessage());
        }
    }

    public function update(TenantOwnerFormRequest $request, string $id)
    {
        try {
            $tenantOwner = $this->tenantOwnerService->getById((int) $id);

            if (!$tenantOwner) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => translate('message.resource_not_found')
                    ], 404);
                }
                return redirect()->back()->with('error', 'Tenant owner not found');
            }

            $updated = $this->tenantOwnerService->update((int) $id, $request->validated());

            if ($updated) {
                $tenantOwner = $this->tenantOwnerService->getById((int) $id);
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'data' => $tenantOwner,
                        'message' => translate('message.updated_successfully')
                    ]);
                }
                
                return redirect()->back()->with('success', 'Tenant owner updated successfully');
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => translate('message.operation_failed')
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to update tenant owner');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => translate('message.operation_failed') . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to update tenant owner: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $tenantOwner = $this->tenantOwnerService->getById((int) $id);

            if (!$tenantOwner) {
                return response()->json([
                    'success' => false,
                    'message' => translate('message.resource_not_found')
                ], 404);
            }

            $deleted = $this->tenantOwnerService->delete((int) $id);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => translate('message.deleted_successfully')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed')
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed') . $e->getMessage()
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
                    'message' => translate('message.action_completed')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed')
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed') . $e->getMessage()
            ], 500);
        }
    }

    public function getByTenant(Request $request, string $tenantId)
    {
        try {
            $filters = $request->only([
                'role', 'status', 'is_super_admin', 'search', 
                'created_by', 'date_from', 'date_to'
            ]);

            // If it's an AJAX request, return JSON
            if ($request->ajax()) {
                $tenantOwners = $this->tenantOwnerService->getTenantOwnersForTenant(
                    (int) $tenantId, 
                    $filters, 
                    $request->get('per_page', 15)
                );

                return response()->json([
                    'success' => true,
                    'data' => $tenantOwners,
                    'message' => translate('message.action_completed')
                ]);
            }

            // For modal view, return the view
            $tenant = \Modules\Tenant\Entities\Tenant::findOrFail($tenantId);
            $tenantOwners = $this->tenantOwnerService->getTenantOwnersForTenant((int) $tenantId, [], 100)->getCollection();

            return view('landlord.tenant-owners.tenant-users', compact('tenant', 'tenantOwners'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => translate('message.operation_failed') . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to retrieve tenant owners: ' . $e->getMessage());
        }
    }

    public function getSuperAdmins(string $tenantId): JsonResponse
    {
        try {
            $superAdmins = $this->tenantOwnerService->getSuperAdminsForTenant((int) $tenantId);

            return response()->json([
                'success' => true,
                'data' => $superAdmins,
                'message' => translate('message.action_completed')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed') . $e->getMessage()
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
                    'message' => translate('message.validation_failed')
                ], 400);
            }

            $tenantOwners = $this->tenantOwnerService->search($query);

            return response()->json([
                'success' => true,
                'data' => $tenantOwners,
                'message' => translate('message.action_completed')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed') . $e->getMessage()
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
                'message' => translate('message.action_completed')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed') . $e->getMessage()
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
                    'message' => translate('message.resource_not_found')
                ], 404);
            }

            $promoted = $this->tenantOwnerService->promoteToSuperAdmin((int) $id);

            if ($promoted) {
                $tenantOwner = $this->tenantOwnerService->getById((int) $id);
                return response()->json([
                    'success' => true,
                    'data' => $tenantOwner,
                    'message' => translate('message.action_completed')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed')
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed') . $e->getMessage()
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
                    'message' => translate('message.resource_not_found')
                ], 404);
            }

            $demoted = $this->tenantOwnerService->demoteFromSuperAdmin((int) $id);

            if ($demoted) {
                $tenantOwner = $this->tenantOwnerService->getById((int) $id);
                return response()->json([
                    'success' => true,
                    'data' => $tenantOwner,
                    'message' => translate('message.action_completed')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed')
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed') . $e->getMessage()
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
                    'message' => translate('message.resource_not_found')
                ], 404);
            }

            $updated = $this->tenantOwnerService->update((int) $id, ['status' => $request->status]);

            if ($updated) {
                $tenantOwner = $this->tenantOwnerService->getById((int) $id);
                return response()->json([
                    'success' => true,
                    'data' => $tenantOwner,
                    'message' => translate('message.updated_successfully')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed')
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.validation_failed'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed') . $e->getMessage()
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
                    'message' => translate('message.resource_not_found')
                ], 404);
            }

            $updated = $this->tenantOwnerService->updatePermissions((int) $id, $request->permissions);

            if ($updated) {
                $tenantOwner = $this->tenantOwnerService->getById((int) $id);
                return response()->json([
                    'success' => true,
                    'data' => $tenantOwner,
                    'message' => translate('message.updated_successfully')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed')
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.validation_failed'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed') . $e->getMessage()
            ], 500);
        }
    }
}
