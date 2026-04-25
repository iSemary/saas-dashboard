<?php

namespace Modules\Auth\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Modules\Auth\Services\Tenant\TenantUserManagementServiceInterface;
use Modules\Auth\Services\Tenant\TenantRoleServiceInterface;
use Modules\Auth\Http\Requests\Tenant\TenantUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TenantUserManagementController extends Controller
{
    protected $userService;
    protected $roleService;

    public function __construct(
        TenantUserManagementServiceInterface $userService,
        TenantRoleServiceInterface $roleService
    )
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        try
        {
            // Check if it's an AJAX/DataTables request
            if ($request->ajax())
            {
                return $this->userService->getDataTables();
            }

            $filters = $request->only(['search', 'role_id', 'status', 'country_id', 'created_from', 'created_to']);
            $perPage = $request->get('per_page', 15);

            // Check if it's an API request
            if ($request->expectsJson())
            {
                $users = $this->userService->getAllUsers($filters, $perPage);

                return response()->json([
                    'success' => true,
                    'data' => $users->items(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                    'statistics' => $this->userService->getUserStatistics()
                ]);
            }

            // Return view for web requests
            $title = translate('users');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('users')],
            ];

            $actionButtons = [
                [
                    'text' => translate('create') . ' ' . translate('user'),
                    'class' => 'btn btn-primary',
                    'link' => route('tenant.users.create')
                ],
            ];

            return view('tenant.auth.users.index', compact('breadcrumbs', 'title', 'actionButtons'));
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
     * Show the form for creating a new user
     */
    public function create()
    {
        try
        {
            $roles = $this->roleService->getAllRoles([], 100);
            $permissions = $this->roleService->getAvailablePermissions();

            $title = translate('create') . ' ' . translate('user');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('users'), 'link' => route('tenant.users.index')],
                ['text' => translate('create')],
            ];

            return view('tenant.auth.users.editor', compact('roles'));
        }
        catch (\Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a newly created user
     */
    public function store(TenantUserRequest $request): JsonResponse
    {
        try
        {
            $data = $request->validated();
            $result = $this->userService->createUser($data);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'redirect' => route('tenant.users.index')
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
     * Display the specified user
     */
    public function show(int $id)
    {
        try
        {
            $user = $this->userService->getUserById($id);

            if (!$user)
            {
                return back()->with('error', translate('user_not_found'));
            }

            $title = translate('view') . ' ' . translate('user');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('users'), 'link' => route('tenant.users.index')],
                ['text' => $user->name],
            ];

            return view('tenant.auth.users.show', compact('breadcrumbs', 'title', 'user'));
        }
        catch (\Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(int $id)
    {
        try
        {
            $user = $this->userService->getUserById($id);

            if (!$user)
            {
                return back()->with('error', translate('user_not_found'));
            }

            $roles = $this->roleService->getAllRoles([], 100);
            $permissions = $this->roleService->getAvailablePermissions();
            $userRoles = $user->roles->pluck('id')->toArray();
            $userPermissions = $user->permissions->pluck('id')->toArray();

            $title = translate('edit') . ' ' . translate('user');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('users'), 'link' => route('tenant.users.index')],
                ['text' => translate('edit')],
            ];

            return view('tenant.auth.users.editor', compact('user', 'roles', 'userRoles'));
        }
        catch (\Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified user
     */
    public function update(TenantUserRequest $request, int $id): JsonResponse
    {
        try
        {
            $data = $request->validated();
            $result = $this->userService->updateUser($id, $data);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'redirect' => route('tenant.users.index')
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
     * Remove the specified user
     */
    public function destroy(int $id): JsonResponse
    {
        try
        {
            $result = $this->userService->deleteUser($id);

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
     * Activate user
     */
    public function activate(int $id): JsonResponse
    {
        try
        {
            $result = $this->userService->activateUser($id);

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
     * Deactivate user
     */
    public function deactivate(int $id): JsonResponse
    {
        try
        {
            $result = $this->userService->deactivateUser($id);

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
     * Bulk activate users
     */
    public function bulkActivate(Request $request): JsonResponse
    {
        try
        {
            $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            $result = $this->userService->bulkActivateUsers($request->user_ids);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data']
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
     * Bulk deactivate users
     */
    public function bulkDeactivate(Request $request): JsonResponse
    {
        try
        {
            $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            $result = $this->userService->bulkDeactivateUsers($request->user_ids);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data']
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
     * Bulk delete users
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try
        {
            $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            $result = $this->userService->bulkDeleteUsers($request->user_ids);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data']
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
     * Reset user password
     */
    public function resetPassword(Request $request, int $id): JsonResponse
    {
        try
        {
            $request->validate([
                'new_password' => 'required|string|min:8|confirmed'
            ]);

            $result = $this->userService->resetUserPassword($id, $request->new_password);

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
     * Send password reset email
     */
    public function sendPasswordResetEmail(int $id): JsonResponse
    {
        try
        {
            $result = $this->userService->sendPasswordResetEmail($id);

            if ($result['success'])
            {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'] ?? []
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
     * Assign roles to user
     */
    public function assignRoles(Request $request, int $id): JsonResponse
    {
        try
        {
            $request->validate([
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,id'
            ]);

            $result = $this->userService->assignRolesToUser($id, $request->roles);

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
     * Sync roles for user
     */
    public function syncRoles(Request $request, int $id): JsonResponse
    {
        try
        {
            $request->validate([
                'roles' => 'sometimes|array',
                'roles.*' => 'exists:roles,id'
            ]);

            $result = $this->userService->syncRolesForUser($id, $request->roles ?? []);

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
     * Get user statistics
     */
    public function statistics(): JsonResponse
    {
        try
        {
            $statistics = $this->userService->getUserStatistics();

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
