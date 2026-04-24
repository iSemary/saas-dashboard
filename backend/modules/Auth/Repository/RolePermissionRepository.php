<?php

namespace Modules\Auth\Repository;

use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;
use Illuminate\Database\Eloquent\Collection;

class RolePermissionRepository implements RolePermissionRepositoryInterface
{
    public function getAllRoles(): Collection
    {
        return Role::with(['permissions', 'users'])->get();
    }

    public function createRole(array $data): Role
    {
        return Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
    }

    public function updateRole(int $id, array $data): Role
    {
        $role = Role::find($id);
        $role->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? $role->guard_name,
        ]);
        
        return $role->fresh();
    }

    public function deleteRole(int $id): bool
    {
        return Role::find($id)->delete();
    }

    public function findRole(int $id): Role|null
    {
        return Role::with(['permissions', 'users'])->find($id);
    }

    public function getAllPermissions(): Collection
    {
        return Permission::with('roles')->get();
    }

    public function createPermission(array $data): Permission
    {
        return Permission::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
    }

    public function updatePermission(int $id, array $data): Permission
    {
        $permission = Permission::find($id);
        $permission->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? $permission->guard_name,
        ]);
        
        return $permission->fresh();
    }

    public function deletePermission(int $id): bool
    {
        return Permission::find($id)->delete();
    }

    public function findPermission(int $id): Permission|null
    {
        return Permission::with('roles')->find($id);
    }

    public function getPermissionsByRole(Role $role): Collection
    {
        return $role->permissions;
    }

    public function assignPermissionsToRole(Role $role, array $permissionIds): bool
    {
        $role->permissions()->sync($permissionIds);
        return true;
    }

    public function getStatistics(): array
    {
        return [
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'roles_with_users' => Role::whereHas('users')->count(),
            'unused_roles' => Role::whereDoesntHave('users')->count(),
        ];
    }
}
