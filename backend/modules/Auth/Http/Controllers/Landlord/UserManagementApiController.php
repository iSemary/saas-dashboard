<?php

namespace Modules\Auth\Http\Controllers\Landlord;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\UserManagementService;
use Modules\Auth\Http\Requests\UserManagementFormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Auth\Entities\User;

class UserManagementApiController extends ApiController
{
    protected UserManagementService $userManagementService;

    public function __construct(UserManagementService $userManagementService)
    {
        $this->userManagementService = $userManagementService;
    }

    /**
     * Get paginated list of users with search and filtering
     */
    public function index(Request $request): JsonResponse
    {
        try 
        {
            $filters = $request->only(['search', 'role', 'status', 'date_from', 'date_to']);
            $perPage = $request->get('per_page', 15);
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $users = $this->userManagementService->getPaginatedUsers($filters, $perPage, [$sortBy => $sortOrder]);

            return $this->return(200, translate('Users retrieved successfully'), [
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ],
                'filters' => $filters,
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving users: ' . $e->getMessage());
        }
    }

    /**
     * Create new user
     */
    public function store(UserManagementFormRequest $request): JsonResponse
    {
        try 
        {
            $userData = $request->validated();
            $user = $this->userManagementService->createUser($userData);

            return $this->return(201, translate('User created successfully'), [
                'user' => $this->formatUserResponse($user)
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Get specific user
     */
    public function show(User $user): JsonResponse
    {
        try 
        {
            $userDetails = $this->userManagementService->getUserDetails($user->id);
            
            return $this->return(200, translate('User retrieved successfully'), [
                'user' => $userDetails
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving user: ' . $e->getMessage());
        }
    }

    /**
     * Update user
     */
    public function update(UserManagementFormRequest $request, User $user): JsonResponse
    {
        try 
        {
            $userData = $request->validated();
            $updatedUser = $this->userManagementService->updateUser($user->id, $userData);

            return $this->return(200, translate('User updated successfully'), [
                'user' => $this->formatUserResponse($updatedUser)
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Delete user
     */
    public function destroy(User $user): JsonResponse
    {
        try 
        {
            $this->userManagementService->deleteUser($user->id);
            
            return $this->return(200, translate('User deleted successfully'));
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Assign roles to user
     */
    public function assignRoles(Request $request, User $user): JsonResponse
    {
        try 
        {
            $request->validate([
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,id'
            ]);

            $this->userManagementService->assignRoles($user->id, $request->roles);
            
            return $this->return(200, translate('Roles assigned successfully'), [
                'user_roles' => $this->userManagementService->getUserRoles($user->id)
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error assigning roles: ' . $e->getMessage());
        }
    }

    /**
     * Remove roles from user
     */
    public function removeRoles(Request $request, User $user): JsonResponse
    {
        try 
        {
            $request->validate([
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,id'
            ]);

            $this->userManagementService->removeRoles($user->id, $request->roles);
            
            return $this->return(200, translate('Roles removed successfully'), [
                'user_roles' => $this->userManagementService->getUserRoles($user->id)
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error removing roles: ' . $e->getMessage());
        }
    }

    /**
     * Get user permissions (including role and direct permissions)
     */
    public function userPermissions(User $user): JsonResponse
    {
        try 
        {
            $permissions = [
                'role_permissions' => $this->userManagementService->getUserRolePermissions($user->id),
                'direct_permissions' => $this->userManagementService->getUserDirectPermissions($user->id),
                'all_permissions' => $this->userManagementService->getUserAllPermissions($user->id),
                'user_roles' => $this->userManagementService->getUserRoles($user->id),
            ];

            return $this->return(200, translate('User permissions retrieved successfully'), $permissions);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving user permissions: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status (activate/deactivate)
     */
    public function toggleStatus(User $user): JsonResponse
    {
        try 
        {
            $newStatus = $this->userManagementService->toggleUserStatus($user->id);
            
            return $this->return(200, translate('User status updated successfully'), [
                'status' => $newStatus,
                'user' => $this->formatUserResponse($user->fresh())
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error updating user status: ' . $e->getMessage());
        }
    }

    /**
     * Get user statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        try 
        {
            $stats = $this->userManagementService->getUserStats();
            
            return $this->return(200, translate('User statistics retrieved successfully'), $stats);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving user statistics: ' . $e->getMessage());
        }
    }

    /**
     * Format user response for API
     */
    private function formatUserResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'status' => $user->status ?? 'active',
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'last_login' => $user->last_login_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'avatar' => $user->getAvatarUrl(),
            'meta' => $user->userMeta()->pluck('value', 'meta_key'),
        ];
    }
}
