<?php

namespace Modules\Auth\Repositories\Tenant;

use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Gate;

class TenantRoleRepository implements TenantRoleRepositoryInterface
{
    public function datatables()
    {
        $rows = Role::query()->withTrashed()
            ->where('guard_name', 'web')
            ->withCount(['permissions', 'users'])
            ->where(function ($q) {
                if (request()->from_date && request()->to_date) {
                    $q->whereBetween('roles.created_at', [request()->from_date, request()->to_date]);
                }
            });

        return DataTables::of($rows)
            ->editColumn('name', function ($row) {
                return ucfirst(str_replace('_', ' ', $row->name));
            })
            ->addColumn('actions', function ($row) {
                $btn = '';
                $type = 'roles';
                $titleType = 'role';

                if (!isset($row->deleted_at) && !$row->deleted_at && Gate::allows('update.' . $type)) {
                    $btn .= '<button type="button" data-modal-title="' . translate("edit") . " " . translate($titleType) . '" data-modal-link="' . route('tenant.roles.edit', $row->id) . '" class="btn-primary mx-1 btn-sm open-edit-modal"><i class="far fa-edit"></i></button>';
                }

                if (!isset($row->deleted_at) && !$row->deleted_at && Gate::allows('delete.' . $type)) {
                    $btn .= '<button type="button" data-delete-type="' . translate($titleType) . '" data-url="' . route('tenant.roles.destroy', $row->id) . '" class="btn-danger mx-1 btn-sm delete-btn"><i class="fa fa-trash"></i></button>';
                }

                if (isset($row->deleted_at) && $row->deleted_at && Gate::allows('restore.' . $type)) {
                    $btn .= '<button type="button" data-restore-type="' . translate($titleType) . '" data-url="' . route('tenant.roles.restore', $row->id) . '" class="btn-warning mx-1 text-white btn-sm restore-btn"><i class="fas fa-redo-alt"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getAllRoles(): Collection
    {
        return Role::with(['permissions', 'users'])
            ->where('guard_name', 'web')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRolesWithPagination(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Role::with(['permissions', 'users'])
            ->where('guard_name', 'web');

        // Apply filters
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('guard_name', 'like', "%{$search}%");
            });
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

    public function createRole(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role->load(['permissions', 'users']);
    }

    public function updateRole(int $id, array $data): Role
    {
        $role = Role::findOrFail($id);

        $role->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? $role->guard_name,
        ]);

        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role->fresh(['permissions', 'users']);
    }

    public function deleteRole(int $id): bool
    {
        $role = Role::findOrFail($id);

        // Remove all permissions first
        $role->syncPermissions([]);

        // Remove role from all users
        DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->delete();

        return $role->delete();
    }

    public function softDeleteRole(int $id): bool
    {
        return Role::where('id', $id)
            ->where('guard_name', 'web')
            ->delete();
    }

    public function restoreRole(int $id): bool
    {
        return Role::where('id', $id)
            ->where('guard_name', 'web')
            ->restore();
    }

    public function findRole(int $id): ?Role
    {
        return Role::with(['permissions', 'users'])
            ->where('guard_name', 'web')
            ->find($id);
    }

    public function getRolesByPermissionIds(array $permissionIds): Collection
    {
        return Role::with(['permissions', 'users'])
            ->whereHas('permissions', function ($query) use ($permissionIds) {
                $query->whereIn('permissions.id', $permissionIds);
            })
            ->where('guard_name', 'web')
            ->get();
    }

    public function assignPermissionsToRole(int $roleId, array $permissionIds): bool
    {
        $role = Role::findOrFail($roleId);

        foreach ($permissionIds as $permissionId) {
            if (!$role->hasPermissionTo($permissionId)) {
                $permission = Permission::findOrFail($permissionId);
                $role->givePermissionTo($permission);
            }
        }

        return true;
    }

    public function removePermissionsFromRole(int $roleId, array $permissionIds): bool
    {
        $role = Role::findOrFail($roleId);

        foreach ($permissionIds as $permissionId) {
            if ($role->hasPermissionTo($permissionId)) {
                $permission = Permission::findOrFail($permissionId);
                $role->revokePermissionTo($permission);
            }
        }

        return true;
    }

    public function syncPermissionsForRole(int $roleId, array $permissionIds): bool
    {
        $role = Role::findOrFail($roleId);
        $role->syncPermissions($permissionIds);

        return true;
    }

    public function getRolesWithUserCount(): Collection
    {
        return Role::withCount('users')
            ->with(['permissions'])
            ->where('guard_name', 'web')
            ->orderBy('users_count', 'desc')
            ->get();
    }

    public function getRoleStatistics(): array
    {
        return [
            'total_roles' => Role::where('guard_name', 'web')->count(),
            'total_permissions_assigned' => DB::table('role_has_permissions')
                ->whereIn('role_id', function ($query) {
                    $query->select('id')
                        ->from('roles')
                        ->where('guard_name', 'web');
                })
                ->distinct('permission_id')
                ->count(),
            'roles_with_users' => Role::whereHas('users')
                ->where('guard_name', 'web')
                ->count(),
            'most_used_role' => Role::with(['permissions'])
                ->withCount('users')
                ->where('guard_name', 'web')
                ->orderBy('users_count', 'desc')
                ->first(),
        ];
    }
}
