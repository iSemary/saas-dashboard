<?php

namespace Modules\Auth\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Modules\Auth\Services\Tenant\TenantRoleServiceInterface;
use Modules\Auth\Http\Requests\Tenant\TenantRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TenantRoleController extends Controller
{
    protected $roleService;

    public function __construct(TenantRoleServiceInterface $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        try
        {
            $filters = $request->only(['search', 'created_from', 'created_to']);
            $perPage = $request->get('per_page', 15);

            // Check if it's an API request
            if ($request->expectsJson())
            {
                $roles = $this->roleService->getAllRoles($filters, $perPage);

                return response()->json([
                    'success' => true,
                    'data' => $roles,
                    'statistics' => $this->roleService->getRoleStatistics()
                ]);
            }

            // Return view for web requests
            $title = translate('roles');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('roles')],
            ];

            $actionButtons = [
                [
                    'text' => translate('create') . ' ' . translate('role'),
                    'class' => 'btn btn-primary',
                    'link' => route('tenant.roles.create')
                ],
            ];

            return view('tenant.auth.roles.index', compact('breadcrumbs', 'title', 'actionButtons'));
        }
        catch (\Exception $e)
        {
            if ($request->expectsJson())
            {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        try
        {
            $permissions = $this->roleService->getAvailablePermissions();
            $permissionsGrouped = $permissions->groupBy(function ($permission)
            {
                $parts = explode('.', $permission->name, 2);
                return count($parts) > 1 ? $parts[1] : 'other';
            });

            $title = translate('create') . ' ' . translate('role');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('roles'), 'link' => route('tenant.roles.index')],
                ['text' => translate('create')],
            ];

            return view('tenant.auth.roles.editor', compact('permissionsGrouped'));
        }
        catch (\Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a newly created role
     */
    public function store(TenantRoleRequest $request): JsonResponse
    {
        try
        {
            $data = $request->validated();
            $result = $this->roleService->createRole($data);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'redirect' => route('tenant.roles.index')
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'errors' => $result['errors'] ?? []
            ], 400);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified role
     */
    public function show(int $id)
    {
        try
        {
            $role = $this->roleService->getRoleById($id);

            if (!$role)
            {
                return back()->with('error', translate('role_not_found'));
            }

            $title = translate('view') . ' ' . translate('role');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('roles'), 'link' => route('tenant.roles.index')],
                ['text' => $role->name],
            ];

            return view('tenant.auth.roles.show', compact('breadcrumbs', 'title', 'role'));
        }
        catch (\Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(int $id)
    {
        try
        {
            $role = $this->roleService->getRoleById($id);

            if (!$role)
            {
                return back()->with('error', translate('role_not_found'));
            }

            $permissions = $this->roleService->getAvailablePermissions();
            $permissionsGrouped = $permissions->groupBy(function ($permission)
            {
                $parts = explode('.', $permission->name, 2);
                return count($parts) > 1 ? $parts[1] : 'other';
            });

            $rolePermissions = $role->permissions->pluck('id')->toArray();

            $title = translate('edit') . ' ' . translate('role');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('roles'), 'link' => route('tenant.roles.index')],
                ['text' => translate('edit')],
            ];

            return view('tenant.auth.roles.editor', compact('role', 'permissionsGrouped', 'rolePermissions'));
        }
        catch (\Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified role
     */
    public function update(TenantRoleRequest $request, int $id): JsonResponse
    {
        try
        {
            $data = $request->validated();
            $result = $this->roleService->updateRole($id, $data);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'redirect' => route('tenant.roles.index')
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'errors' => $result['errors'] ?? []
            ], 400);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(int $id): JsonResponse
    {
        try
        {
            $result = $this->roleService->deleteRole($id);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a soft deleted role
     */
    public function restore(int $id): JsonResponse
    {
        try
        {
            $result = $this->roleService->restoreRole($id);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign permissions to a role
     */
    public function assignPermissions(Request $request, int $id): JsonResponse
    {
        try
        {
            $request->validate([
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $result = $this->roleService->assignPermissionsToRole($id, $request->permissions);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync permissions for a role
     */
    public function syncPermissions(Request $request, int $id): JsonResponse
    {
        try
        {
            $request->validate([
                'permissions' => 'sometimes|array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $result = $this->roleService->syncPermissionsForRole($id, $request->permissions ?? []);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role statistics
     */
    public function statistics(): JsonResponse
    {
        try
        {
            $statistics = $this->roleService->getRoleStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics
            ], 200);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
