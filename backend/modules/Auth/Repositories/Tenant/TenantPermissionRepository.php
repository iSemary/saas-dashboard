<?php

namespace Modules\Auth\Repositories\Tenant;

use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Helpers\TableHelper;

class TenantPermissionRepository implements TenantPermissionRepositoryInterface
{
    public function datatables()
    {
        $rows = Permission::query()->withTrashed()
            ->where('guard_name', 'web')
            ->withCount('roles')
            ->selectRaw("permissions.*, 
                SUBSTRING_INDEX(permissions.name, '.', -1) as resource,
                SUBSTRING_INDEX(permissions.name, '.', 1) as action")
            ->where(function ($q) {
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, 'permissions', [request()->from_date, request()->to_date]);
                }
            });

        return DataTables::of($rows)
            ->editColumn('resource', function ($row) {
                return ucfirst($row->resource);
            })
            ->editColumn('action', function ($row) {
                return ucfirst($row->action);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'tenant.permissions.edit',
                    deleteRoute: 'tenant.permissions.destroy',
                    type: 'permissions',
                    titleType: 'permission',
                    showIconsOnly: true
                );
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getAllPermissions(): Collection
    {
        return Permission::with(['roles'])
            ->where('guard_name', 'web')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPermissionsWithPagination(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Permission::with(['roles'])
            ->where('guard_name', 'web');

        // Apply filters
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('guard_name', 'like', "%{$search}%");
            });
        }

        if (isset($filters['resource'])) {
            $query->where('name', 'like', "%{$filters['resource']}%");
        }

        if (isset($filters['action'])) {
            $query->where('name', 'like', "{$filters['action']}.%");
        }

        if (isset($filters['created_from'])) {
            $query->where('created_at', '>=', $filters['created_from']);
        }

        if (isset($filters['created_to'])) {
            $query->where('created_at', '<=', $filters['created_to']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function createPermission(array $data): Permission
    {
        $permission = Permission::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        if (isset($data['roles']) && is_array($data['roles'])) {
            $permission->syncRoles($data['roles']);
        }

        return $permission->load('roles');
    }

    public function updatePermission(int $id, array $data): Permission
    {
        $permission = Permission::findOrFail($id);
        
        $permission->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? $permission->guard_name,
        ]);

        if (isset($data['roles']) && is_array($data['roles'])) {
            $permission->syncRoles($data['roles']);
        }

        return $permission->fresh(['roles']);
    }

    public function deletePermission(int $id): bool
    {
        $permission = Permission::findOrFail($id);

        // Remove all role assignments first
        $permission->syncRoles([]);

        // Remove permission from users (direct assignments)
        DB::table('model_has_permissions')
            ->where('permission_id', $permission->id)
            ->delete();

        return $permission->delete();
    }

    public function softDeletePermission(int $id): bool
    {
        return Permission::where('id', $id)
            ->where('guard_name', 'web')
            ->delete();
    }

    public function restorePermission(int $id): bool
    {
        return Permission::where('id', $id)
            ->where('guard_name', 'web')
            ->restore();
    }

    public function findPermission(int $id): ?Permission
    {
        return Permission::with(['roles'])
            ->where('guard_name', 'web')
            ->find($id);
    }

    public function getPermissionsByRoleIds(array $roleIds): Collection
    {
        return Permission::with(['roles'])
            ->whereHas('roles', function ($query) use ($roleIds) {
                $query->whereIn('roles.id', $roleIds);
            })
            ->where('guard_name', 'web')
            ->get();
    }

    public function getPermissionsGroupedByResource(): Collection
    {
        return Permission::with(['roles'])
            ->where('guard_name', 'web')
            ->get()
            ->groupBy(function ($permission) {
                $nameParts = explode('.', $permission->name, 2);
                return count($nameParts) > 1 ? $nameParts[1] : 'unknown';
            });
    }

    public function getPermissionsByAction(string $action): Collection
    {
        return Permission::with(['roles'])
            ->where('name', 'like', "{$action}.%")
            ->where('guard_name', 'web')
            ->get();
    }

    public function bulkCreateResourcePermissions(string $resource, array $actions = ['view', 'create', 'update', 'delete']): Collection
    {
        $permissions = [];

        foreach ($actions as $action) {
            $permissionName = "{$action}.{$resource}";
            
            // Check if permission already exists
            $existingPermission = Permission::where('name', $permissionName)
                ->where('guard_name', 'web')
                ->first();

            if (!$existingPermission) {
                $permissions[] = Permission::create([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            } else {
                $permissions[] = $existingPermission;
            }
        }

        return collect($permissions);
    }

    public function assignPermissionToRoles(int $permissionId, array $roleIds): bool
    {
        $permission = Permission::findOrFail($permissionId);
        $roles = Role::whereIn('id', $roleIds)
            ->where('guard_name', 'web')
            ->get();

        foreach ($roles as $role) {
            if (!$role->hasPermissionTo($permissionId)) {
                $role->givePermissionTo($permission);
            }
        }

        return true;
    }

    public function removePermissionFromRoles(int $permissionId, array $roleIds): bool
    {
        $permission = Permission::findOrFail($permissionId);
        $roles = Role::whereIn('id', $roleIds)
            ->where('guard_name', 'web')
            ->get();

        foreach ($roles as $role) {
            if ($role->hasPermissionTo($permissionId)) {
                $role->revokePermissionTo($permission);
            }
        }

        return true;
    }

    public function getPermissionStatistics(): array
    {
        return [
            'total_permissions' => Permission::where('guard_name', 'web')->count(),
            'total_role_assignments' => DB::table('role_has_permissions')->count(),
            'most_assigned_permission' => Permission::with(['roles'])
                ->withCount('roles')
                ->where('guard_name', 'web')
                ->orderBy('roles_count', 'desc')
                ->first(),
            'permissions_by_action' => Permission::where('guard_name', 'web')
                ->get()
                ->groupBy(function ($permission) {
                    $parts = explode('.', $permission->name, 2);
                    return $parts[0] ?? 'unknown';
                })
                ->map(function ($group) {
                    return $group->count();
                }),
            'resources_with_permissions' => Permission::where('guard_name', 'web')
                ->get()
                ->groupBy(function ($permission) {
                    $parts = explode('.', $permission->name, 2);
                    return count($parts) > 1 ? $parts[1] : 'unknown';
                })
                ->count(),
        ];
    }
}
