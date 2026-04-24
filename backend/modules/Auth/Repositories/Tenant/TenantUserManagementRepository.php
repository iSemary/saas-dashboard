<?php

namespace Modules\Auth\Repositories\Tenant;

use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;
use Modules\Tenant\Helper\TenantHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use App\Helpers\TableHelper;

class TenantUserManagementRepository implements TenantUserManagementRepositoryInterface
{
    public function datatables()
    {
        $rows = User::query()->withTrashed()
            ->whereNotNull('customer_id')
            ->with('roles')
            ->where(function ($q) {
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, 'users', [request()->from_date, request()->to_date]);
                }
            });

        return DataTables::of($rows)
            ->addColumn('roles', function ($row) {
                return $row->roles->pluck('name')->map(function($role) {
                    return '<span class="badge badge-info">' . ucfirst(str_replace('_', ' ', $role)) . '</span>';
                })->implode(' ');
            })
            ->editColumn('status', function ($row) {
                return $row->deleted_at 
                    ? '<span class="badge badge-danger">Inactive</span>' 
                    : '<span class="badge badge-success">Active</span>';
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'tenant.users.edit',
                    deleteRoute: 'tenant.users.destroy',
                    type: 'users',
                    titleType: 'user',
                    showIconsOnly: true
                );
            })
            ->rawColumns(['roles', 'status', 'actions'])
            ->make(true);
    }

    public function getAllUsers(): Collection
    {
        return User::with(['roles', 'permissions', 'country', 'language'])
            ->whereNotNull('customer_id')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUsersWithPagination(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $tenantKey = TenantHelper::getSubDomain();
        $query = User::with(['roles', 'permissions'])
            ->whereNotNull('customer_id');

        // Apply filters
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if (isset($filters['role_id'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('id', $filters['role_id']);
            });
        }

        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->whereNotNull('email_verified_at');
            } elseif ($filters['status'] === 'inactive') {
                $query->whereNull('email_verified_at');
            }
        }

        if (isset($filters['created_from'])) {
            $query->where('created_at', '>=', $filters['created_from']);
        }

        if (isset($filters['created_to'])) {
            $query->where('created_at', '<=', $filters['created_to']);
        }

        if (isset($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function createUser(array $data): User
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = User::create([
            'customer_id' => $data['customer_id'] ?? 1,
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'] ?? null,
            'password' => $data['password'],
            'country_id' => $data['country_id'] ?? null,
            'language_id' => $data['language_id'] ?? 1,
            'factor_authenticate' => $data['factor_authenticate'] ?? 0,
        ]);

        // Assign roles if provided
        if (isset($data['roles']) && is_array($data['roles'])) {
            $roleIds = [];
            foreach ($data['roles'] as $role) {
                if (is_numeric($role)) {
                    $roleIds[] = $role;
                } elseif (is_string($role)) {
                    $roleModel = Role::where('name', $role)->first();
                    if ($roleModel) {
                        $roleIds[] = $roleModel->id;
                    }
                }
            }
            $user->syncRoles($roleIds);
        }

        // Assign direct permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $permissionIds = [];
            foreach ($data['permissions'] as $permission) {
                if (is_numeric($permission)) {
                    $permissionIds[] = $permission;
                } elseif (is_string($permission)) {
                    $permissionModel = Permission::where('name', $permission)->first();
                    if ($permissionModel) {
                        $permissionIds[] = $permissionModel->id;
                    }
                }
            }
            $user->syncPermissions($permissionIds);
        }

        return $user->fresh(['roles', 'permissions']);
    }

    public function updateUser(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        $updateData = [
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'username' => $data['username'] ?? $user->username,
             'country_id' => $data['country_id'] ?? $user->country_id,
            'language_id' => $data['language_id'] ?? $user->language_id,
        ];

        // Handle password update
        if (isset($data['password']) && !empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        // Handle 2FA setting
        if (isset($data['factor_authenticate'])) {
            $updateData['factor_authenticate'] = $data['factor_authenticate'];
        }

        $user->update($updateData);

        // Update roles if provided
        if (isset($data['roles']) && is_array($data['roles'])) {
            $roleIds = [];
            foreach ($data['roles'] as $role) {
                if (is_numeric($role)) {
                    $roleIds[] = $role;
                } elseif (is_string($role)) {
                    $roleModel = Role::where('name', $role)->first();
                    if ($roleModel) {
                        $roleIds[] = $roleModel->id;
                    }
                }
            }
            $user->syncRoles($roleIds);
        }

        // Update direct permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $permissionIds = [];
            foreach ($data['permissions'] as $permission) {
                if (is_numeric($permission)) {
                    $permissionIds[] = $permission;
                } elseif (is_string($permission)) {
                    $permissionModel = Permission::where('name', $permission)->first();
                    if ($permissionModel) {
                        $permissionIds[] = $permissionModel->id;
                    }
                }
            }
            $user->syncPermissions($permissionIds);
        }

        return $user->fresh(['roles', 'permissions']);
    }

    public function deleteUser(int $id): bool
    {
        $user = User::findOrFail($id);

        // Remove all role assignments
        $user->syncRoles([]);

        // Remove all direct permission assignments
        $user->syncPermissions([]);

        // Delete user meta
        $user->userMeta()->delete();

        return $user->delete();
    }

    public function softDeleteUser(int $id): bool
    {
        return User::where('id', $id)
            ->whereNotNull('customer_id')
            ->delete();
    }

    public function restoreUser(int $id): bool
    {
        return User::where('id', $id)
            ->whereNotNull('customer_id')
            ->restore();
    }

    public function findUser(int $id): ?User
    {
        return User::with(['roles', 'permissions', 'country', 'language'])
            ->whereNotNull('customer_id')
            ->find($id);
    }

    public function assignRolesToUser(int $userId, array $roleIds): bool
    {
        $user = User::findOrFail($userId);
        
        foreach ($roleIds as $roleId) {
            $role = Role::findOrFail($roleId);
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
        }

        return true;
    }

    public function removeRolesFromUser(int $userId, array $roleIds): bool
    {
        $user = User::findOrFail($userId);
        
        foreach ($roleIds as $roleId) {
            $role = Role::findOrFail($roleId);
            if ($user->hasRole($role)) {
                $user->removeRole($role);
            }
        }

        return true;
    }

    public function syncRolesForUser(int $userId, array $roleIds): bool
    {
        $user = User::findOrFail($userId);
        $user->syncRoles($roleIds);
        
        return true;
    }

    public function assignPermissionsToUser(int $userId, array $permissionIds): bool
    {
        $user = User::findOrFail($userId);
        
        foreach ($permissionIds as $permissionId) {
            $permission = Permission::findOrFail($permissionId);
            if (!$user->hasPermissionTo($permission)) {
                $user->givePermissionTo($permission);
            }
        }

        return true;
    }

    public function removePermissionsFromUser(int $userId, array $permissionIds): bool
    {
        $user = User::findOrFail($userId);
        
        foreach ($permissionIds as $permissionId) {
            $permission = Permission::findOrFail($permissionId);
            if ($user->hasPermissionTo($permission)) {
                $user->revokePermissionTo($permission);
            }
        }

        return true;
    }

    public function syncPermissionsForUser(int $userId, array $permissionIds): bool
    {
        $user = User::findOrFail($userId);
        $user->syncPermissions($permissionIds);
        
        return true;
    }

    public function getUsersByRole(int $roleId): Collection
    {
        return User::with(['roles', 'permissions'])
            ->whereHas('roles', function ($query) use ($roleId) {
                $query->where('id', $roleId);
            })
            ->whereNotNull('customer_id')
            ->get();
    }

    public function getUsersByRoleName(string $roleName): Collection
    {
        return User::with(['roles', 'permissions'])
            ->whereHas('roles', function ($query) use ($roleName) {
                $query->where('name', $roleName);
            })
            ->whereNotNull('customer_id')
            ->get();
    }

    public function getActiveUsers(): Collection
    {
        return User::with(['roles', 'permissions'])
            ->whereNotNull('customer_id')
            ->whereNotNull('email_verified_at')
            ->get();
    }

    public function getInactiveUsers(): Collection
    {
        return User::with(['roles', 'permissions'])
            ->whereNotNull('customer_id')
            ->whereNull('email_verified_at')
            ->get();
    }

    public function activateUser(int $userId): bool
    {
        return User::where('id', $userId)
            ->whereNotNull('customer_id')
            ->update(['email_verified_at' => now()]);
    }

    public function deactivateUser(int $userId): bool
    {
        return User::where('id', $userId)
            ->whereNotNull('customer_id')
            ->update(['email_verified_at' => null]);
    }

    public function bulkActivateUsers(array $userIds): bool
    {
        return User::whereIn('id', $userIds)
            ->whereNotNull('customer_id')
            ->update(['email_verified_at' => now()]);
    }

    public function bulkDeactivateUsers(array $userIds): bool
    {
        return User::whereIn('id', $userIds)
            ->whereNotNull('customer_id')
            ->update(['email_verified_at' => null]);
    }

    public function bulkDeleteUsers(array $userIds): bool
    {
        $users = User::whereIn('id', $userIds)
            ->whereNotNull('customer_id')
            ->get();

        foreach ($users as $user) {
            $this->deleteUser($user->id);
        }

        return true;
    }

    public function getUserStatistics(): array
    {
        return [
            'total_users' => User::whereNotNull('customer_id')->count(),
            'active_users' => User::whereNotNull('customer_id')->whereNotNull('email_verified_at')->count(),
            'inactive_users' => User::whereNotNull('customer_id')->whereNull('email_verified_at')->count(),
            'users_with_2fa' => User::whereNotNull('customer_id')->where('factor_authenticate', 1)->count(),
            'most_common_role' => User::with(['roles'])
                ->withCount(['roles'])
                ->whereNotNull('customer_id')
                ->orderBy('roles_count', 'desc')
                ->first(),
            'users_by_country' => User::whereNotNull('customer_id')
                ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
                ->selectRaw('countries.name, COUNT(*) as count')
                ->groupBy('users.country_id', 'countries.name')
                ->get(),
        ];
    }

    public function searchUsers(string $query): Collection
    {
        return User::with(['roles', 'permissions'])
            ->whereNotNull('customer_id')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%");
            })
            ->get();
    }

    public function getUsersCreatedBetween(string $startDate, string $endDate): Collection
    {
        return User::with(['roles', 'permissions'])
            ->whereNotNull('customer_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }
}
