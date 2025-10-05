<?php

namespace Modules\Auth\Http\Controllers\Landlord;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\RolePermissionService;
use Modules\Auth\Http\Requests\RolePermissionFormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;

class RolePermissionApiController extends ApiController
{
    protected RolePermissionService $rolePermissionService;

    public function __construct(RolePermissionService $rolePermissionService)
    {
        $this->rolePermissionService = $rolePermissionService;
    }

    /**
     * Get all roles with permissions
     */
    public function roles(Request $request): JsonResponse
    {
        try 
        {
            $roles = $this->rolePermissionService->getRolesWithPermissions();
            
            return $this->return(200, translate('Roles retrieved successfully'), [
                'roles' => $roles
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving roles: ' . $e->getMessage());
        }
    }

    /**
     * Create new role
     */
    public function createRole(RolePermissionFormRequest $request): JsonResponse
    {
        try 
        {
            $roleData = $request->validated();
            $role = $this->rolePermissionService->createRole($roleData);

            return $this->return(201, translate('Role created successfully'), [
                'role' => $this->formatRoleResponse($role)
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error creating role: ' . $e->getMessage());
        }
    }

    /**
     * Get specific role with permissions
     */
    public function showRole(Role $role): JsonResponse
    {
        try 
        {
            $roleDetails = $this->rolePermissionService->getRoleDetails($role->id);
            
            return $this->return(200, translate('Role retrieved successfully'), [
                'role' => $roleDetails
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving role: ' . $e->getMessage());
        }
    }

    /**
     * Update role
     */
    public function updateRole(RolePermissionFormRequest $request, Role $role): JsonResponse
    {
        try 
        {
            $roleData = $request->validated();
            $updatedRole = $this->rolePermissionService->updateRole($role->id, $roleData);

            return $this->return(200, translate('Role updated successfully'), [
                'role' => $this->formatRoleResponse($updatedRole)
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error updating role: ' . $e->getMessage());
        }
    }

    /**
     * Delete role
     */
    public function deleteRole(Role $role): JsonResponse
    {
        try 
        {
            $this->rolePermissionService->deleteRole($role->id);
            
            return $this->return(200, translate('Role deleted successfully'));
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error deleting role: ' . $e->getMessage());
        }
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(Request $request, Role $role): JsonResponse
    {
        try 
        {
            $request->validate([
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $this->rolePermissionService->assignPermissionsToRole($role->id, $request->permissions);
            
            return $this->return(200, translate('Permissions assigned successfully'), [
                'role_permissions' => $this->rolePermissionService->getRolePermissions($role->id)
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error assigning permissions: ' . $e->getMessage());
        }
    }

    /**
     * Get all permissions
     */
    public function permissions(Request $request): JsonResponse
    {
        try 
        {
            $filters = $request->only(['search', 'group']);
            $permissions = $this->rolePermissionService->getPermissionsWithGroups($filters);
            
            return $this->return(200, translate('Permissions retrieved successfully'), [
                'permissions' => $permissions
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving permissions: ' . $e->getMessage());
        }
    }

    /**
     * Create new permission
     */
    public function createPermission(RolePermissionFormRequest $request): JsonResponse
    {
        try 
        {
            $permissionData = $request->validated();
            $permission = $this->rolePermissionService->createPermission($permissionData);

            return $this->return(201, translate('Permission created successfully'), [
                'permission' => $this->formatPermissionResponse($permission)
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error creating permission: ' . $e->getMessage());
        }
    }

    /**
     * Update permission
     */
    public function updatePermission(RolePermissionFormRequest $request, Permission $permission): JsonResponse
    {
        try 
        {
            $permissionData = $request->validated();
            $updatedPermission = $this->rolePermissionService->updatePermission($permission->id, $permissionData);

            return $this->return(200, translate('Permission updated successfully'), [
                'permission' => $this->formatPermissionResponse($updatedPermission)
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error updating permission: ' . $e->getMessage());
        }
    }

    /**
     * Delete permission
     */
    public function deletePermission(Permission $permission): JsonResponse
    {
        try 
        {
            $this->rolePermissionService->deletePermission($permission->id);
            
            return $this->return(200, translate('Permission deleted successfully'));
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error deleting permission: ' . $e->getMessage());
        }
    }

    /**
     * Get permission statistics
     */
    public function getPermissionStats(Request $request): JsonResponse
    {
        try 
        {
            $stats = $this->rolePermissionService->getPermissionStatistics();
            
            return $this->return(200, translate('Permission statistics retrieved successfully'), $stats);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving permission statistics: ' . $e->getMessage());
        }
    }

    /**
     * Format role response for API
     */
    private function formatRoleResponse(Role $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'permissions' => $role->permissions->pluck('name')->toArray(),
            'permission_count' => $role->permissions->count(),
            'users_count' => $role->users->count(),
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ];
    }

    /**
     * Format permission response for API
     */
    private function formatPermissionResponse(Permission $permission): array
    {
        return [
            'id' => $permission->id,
            'name' => $permission->name,
            'guard_name' => $permission->guard_name,
            'group' => $this->extractPermissionGroup($permission->name),
            'roles_count' => $permission->roles->count(),
            'created_at' => $permission->created_at,
            'updated_at' => $permission->updated_at,
        ];
    }

    /**
     * Extract permission group from permission name
     */
    private function extractPermissionGroup(string $permissionName): string
    {
        $parts = explode('.', $permissionName);
        return $parts[0] ?? 'general';
    }
}
