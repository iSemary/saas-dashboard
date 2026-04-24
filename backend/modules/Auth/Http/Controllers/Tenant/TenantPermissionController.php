<?php

namespace Modules\Auth\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Modules\Auth\Services\Tenant\TenantPermissionServiceInterface;
use Modules\Auth\Http\Requests\Tenant\TenantPermissionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TenantPermissionController extends Controller
{
    protected $permissionService;

    public function __construct(TenantPermissionServiceInterface $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of permissions
     */
    public function index(Request $request)
    {
        try
        {
            // Check if it's an AJAX/DataTables request
            if ($request->ajax())
            {
                return $this->permissionService->getDataTables();
            }

            $filters = $request->only(['search', 'resource', 'action', 'created_from', 'created_to']);
            $perPage = $request->get('per_page', 15);

            // Check if it's an API request
            if ($request->expectsJson())
            {
                $permissions = $this->permissionService->getAllPermissions($filters, $perPage);

                return response()->json([
                    'success' => true,
                    'data' => $permissions,
                    'statistics' => $this->permissionService->getPermissionStatistics()
                ]);
            }

            // Return view for web requests
            $title = translate('permissions');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('permissions')],
            ];

            $actionButtons = [
                [
                    'text' => translate('create') . ' ' . translate('permission'),
                    'class' => 'btn btn-primary',
                    'link' => route('tenant.permissions.create')
                ],
                [
                    'text' => translate('bulk_create'),
                    'class' => 'btn btn-secondary',
                    'link' => route('tenant.permissions.bulk-create')
                ],
            ];

            return view('tenant.auth.permissions.index', compact('breadcrumbs', 'title', 'actionButtons'));
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
     * Show the form for creating a new permission
     */
    public function create()
    {
        try
        {
            $title = translate('create') . ' ' . translate('permission');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('permissions'), 'link' => route('tenant.permissions.index')],
                ['text' => translate('create')],
            ];

            return view('tenant.auth.permissions.editor');
        }
        catch (\Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a newly created permission
     */
    public function store(TenantPermissionRequest $request): JsonResponse
    {
        try
        {
            $data = $request->validated();
            $result = $this->permissionService->createPermission($data);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'redirect' => route('tenant.permissions.index')
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
     * Display the specified permission
     */
    public function show(int $id)
    {
        try
        {
            $permission = $this->permissionService->getPermissionById($id);

            if (!$permission)
            {
                return back()->with('error', translate('permission_not_found'));
            }

            $title = translate('view') . ' ' . translate('permission');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('permissions'), 'link' => route('tenant.permissions.index')],
                ['text' => $permission->name],
            ];

            return view('tenant.auth.permissions.show', compact('breadcrumbs', 'title', 'permission'));
        }
        catch (\Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified permission
     */
    public function edit(int $id)
    {
        try
        {
            $permission = $this->permissionService->getPermissionById($id);

            if (!$permission)
            {
                return back()->with('error', translate('permission_not_found'));
            }

            $title = translate('edit') . ' ' . translate('permission');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('permissions'), 'link' => route('tenant.permissions.index')],
                ['text' => translate('edit')],
            ];

            return view('tenant.auth.permissions.editor', compact('permission'));
        }
        catch (\Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified permission
     */
    public function update(TenantPermissionRequest $request, int $id): JsonResponse
    {
        try
        {
            $data = $request->validated();
            $result = $this->permissionService->updatePermission($id, $data);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'redirect' => route('tenant.permissions.index')
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
     * Remove the specified permission
     */
    public function destroy(int $id): JsonResponse
    {
        try
        {
            $result = $this->permissionService->deletePermission($id);

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
     * Show bulk create form
     */
    public function bulkCreateForm()
    {
        try
        {
            $title = translate('bulk_create') . ' ' . translate('permissions');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('permissions'), 'link' => route('tenant.permissions.index')],
                ['text' => translate('bulk_create')],
            ];

            return view('tenant.auth.permissions.bulk-create', compact('breadcrumbs', 'title'));
        }
        catch (\Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk create permissions for a resource
     */
    public function bulkCreate(Request $request): JsonResponse
    {
        try
        {
            $request->validate([
                'resource' => 'required|string|max:255|regex:/^[a-z_]+$/',
                'actions' => 'required|array',
                'actions.*' => 'in:view,create,update,delete'
            ]);

            $result = $this->permissionService->bulkCreateResourcePermissions(
                $request->resource,
                $request->actions
            );

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'redirect' => route('tenant.permissions.index')
                ], 201);
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
     * Get permissions grouped by resource
     */
    public function grouped(): JsonResponse
    {
        try
        {
            $permissionsGrouped = $this->permissionService->getPermissionsGroupedByResource();

            return response()->json([
                'success' => true,
                'data' => $permissionsGrouped
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

    /**
     * Get permission statistics
     */
    public function statistics(): JsonResponse
    {
        try
        {
            $statistics = $this->permissionService->getPermissionStatistics();

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
