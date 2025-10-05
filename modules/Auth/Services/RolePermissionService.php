<?php

namespace Modules\Auth\Services;

use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Repository\RolePermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RolePermissionService
{
    protected RolePermissionRepositoryInterface $rolePermissionRepository;

    public function __construct(RolePermissionRepositoryInterface $rolePermissionRepository)
    {
        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    /**
     * Get roles with permissions
     */
    public function getRolesWithPermissions(): array
    {
        return Role::with('permissions')->get()->map(function ($role) {
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
        })->toArray();
    }

    /**
     * Create new role
     */
    public function createRole(array $roleData): Role
    {
        DB::beginTransaction();
        
        try {
            $role = Role::create([
                'name' => $roleData['name'],
                'guard_name' => $roleData['guard_name'] ?? 'web',
            ]);

            if (isset($roleData['permissions'])) {
                $role->permissions()->sync($roleData['permissions']);
            }

            DB::commit();
            
            return $role->load('permissions');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update role
     */
    public function updateRole(int $roleId, array $roleData): Role
    {
        DB::beginTransaction();
        
        try {
            $role = Role::find($roleId);
            if (!$role) {
                throw new \Exception('Role not found');
            }

            $role->update([
                'name' => $roleData['name'],
                'guard_name' => $roleData['guard_name'] ?? $role->guard_name,
            ]);

            if (isset($roleData['permissions'])) {
                $role->permissions()->sync($roleData['permissions']);
            }

            DB::commit();
            
            return $role->fresh(['permissions']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete role
     */
    public function deleteRole(int $roleId): bool
    {
        DB::beginTransaction();
        
        try {
            $role = Role::find($roleId);
            if (!$role) {
                throw new \Exception('Role not found');
            }

            // Check if role has users assigned
            if ($role->users()->count() > 0) {
                throw new \Exception('Cannot delete role with assigned users');
            }

            $role->delete();

            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissionsToRole(int $roleId, array $permissionIds): bool
    {
        $role = Role::find($roleId);
        if (!$role) {
            throw new \Exception('Role not found');
        }

        $role->permissions()->sync($permissionIds);
        
        return true;
    }

    /**
     * Get permissions with groups
     */
    public function getPermissionsWithGroups(array $filters = []): array
    {
        $query = Permission::query();

        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['group'])) {
            $query->where('name', 'like', "{$filters['group']}%");
        }

        return $query->get()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'guard_name' => $permission->guard_name,
                'group' => $this->extractPermissionGroup($permission->name),
                'roles_count' => $permission->roles->count(),
                'created_at' => $permission->created_at,
                'updated_at' => $permission->updated_at,
            ];
        })->toArray();
    }

    /**
     * Create new permission
     */
    public function createPermission(array $permissionData): Permission
    {
        return Permission::create([
            'name' => $permissionData['name'],
            'guard_name' => $permissionData['guard_name'] ?? 'web',
        ]);
    }

    /**
     * Update permission
     */
    public function updatePermission(int $permissionId, array $permissionData): Permission
    {
        $permission = Permission::find($permissionId);
        if (!$permission) {
            throw new \Exception('Permission not found');
        }

        $permission->update([
            'name' => $permissionData['name'],
            'guard_name' => $permissionData['guard_name'] ?? $permission->guard_name,
        ]);

        return $permission->fresh();
    }

    /**
     * Delete permission
     */
    public function deletePermission(int $permissionId): bool
    {
        $permission = Permission::find($permissionId);
        if (!$permission) {
            throw new \Exception('Permission not found');
        }

        return $permission->delete();
    }

    /**
     * Get role details
     */
    public function getRoleDetails(int $roleId): array|null
    {
        $role = Role::with(['permissions', 'users'])->find($roleId);
        
        if (!$role) {
            return null;
        }

        return [
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'permissions' => $role->permissions->toArray(),
            'users' => $role->users->toArray(),
            'permission_count' => $role->permissions->count(),
            'users_count' => $role->users->count(),
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ];
    }

    /**
     * Get permission statistics
     */
    public function getPermissionStatistics(): array
    {
        return [
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'permissions_by_group' => Permission::selectRaw('SUBSTRING_INDEX(name, ".", 1) as group_name, COUNT(*) as count')
                ->groupBy('group_name')
                ->get()
                ->pluck('count', 'group_name')
                ->toArray(),
            'unused_roles' => Role::whereDoesntHave('users')->count(),
            'roles_with_users' => Role::whereHas('users')->count(),
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
