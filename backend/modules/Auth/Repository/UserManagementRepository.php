<?php

namespace Modules\Auth\Repository;

use Modules\Auth\Entities\User;
use Modules\Auth\Repository\UserManagementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class UserManagementRepository implements UserManagementRepositoryInterface
{
    public function getUsersWithFilters(array $filters = [], int $perPage = 15, array $orderBy = []): LengthAwarePaginator
    {
        $query = User::with(['roles', 'permissions', 'userMeta']);

        // Apply filters
        if (isset($filters['search']))
        {
            $search = $filters['search'];
            $query->where(function ($q) use ($search)
            {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if (isset($filters['role']))
        {
            $query->whereHas('roles', function ($q) use ($filters)
            {
                $q->where('id', $filters['role']);
            });
        }

        if (isset($filters['status']))
        {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from']))
        {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']))
        {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Apply ordering
        foreach ($orderBy as $column => $direction)
        {
            $query->orderBy($column, $direction);
        }

        // Default ordering if none specified
        if (empty($orderBy))
        {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    public function createUser(array $userData): User
    {
        return User::create($userData);
    }

    public function updateUser(int $userId, array $userData): bool
    {
        $user = User::find($userId);
        if (!$user)
        {
            return false;
        }

        return $user->update($userData);
    }

    public function deleteUser(int $userId): bool
    {
        $user = User::find($userId);
        if (!$user)
        {
            return false;
        }

        return $user->delete();
    }

    public function findUser(int $userId): User|null
    {
        return User::find($userId);
    }

    public function findUserWithRelations(int $userId): User|null
    {
        return User::with(['roles', 'permissions', 'userMeta'])->find($userId);
    }

    public function findUserWithRoles(int $userId): User|null
    {
        return User::with('roles')->find($userId);
    }

    public function findUserWithDirectPermissions(int $userId): User|null
    {
        return User::with('permissions')->find($userId);
    }

    public function findUserWithRolesAndPermissions(int $userId): User|null
    {
        return User::with(['roles.permissions', 'permissions'])->find($userId);
    }

    public function setUserMeta(int $userId, array $metaData): bool
    {
        $user = User::find($userId);
        if (!$user)
        {
            return false;
        }

        foreach ($metaData as $key => $value)
        {
            // Update or create user meta
            $user->userMeta()->updateOrCreate(
                ['meta_key' => $key],
                ['value' => $value]
            );
        }

        return true;
    }

    public function getUserStatistics(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->orWhereNull('status')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_this_week' => User::whereDate('created_at', '>=', now()->startOfWeek())->count(),
            'new_users_this_month' => User::whereDate('created_at', '>=', now()->startOfMonth())->count(),
            'users_with_roles' => User::whereHas('roles')->count(),
            'users_without_roles' => User::whereDoesntHave('roles')->count(),
        ];
    }

    public function searchUsers(string $query, int $limit = 10): array
    {
        return User::where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%")
                  ->limit($limit)
                  ->get(['id', 'name', 'email', 'username'])
                  ->toArray();
    }

    public function getUsersByRole(string $roleName): array
    {
        return User::whereHas('roles', function ($q) use ($roleName)
        {
            $q->where('name', $roleName);
        })->with(['roles', 'permissions'])
          ->get()
          ->toArray();
    }

    public function getActiveUsersCount(): int
    {
        return User::where('status', 'active')->orWhereNull('status')->count();
    }

    public function getInactiveUsersCount(): int
    {
        return User::where('status', 'inactive')->count();
    }

    public function getNewUsersCount(int $days = 30): int
    {
        return User::where('created_at', '>=', now()->subDays($days))->count();
    }

    /**
     * Get DataTables data for users
     */
    public function getDataTables()
    {
        $users = User::query()->with(['roles', 'permissions']);

        return DataTables::of($users)
            ->addColumn('roles_list', function ($user)
            {
                return $user->roles->pluck('name')->implode(', ');
            })
            ->addColumn('permissions_count', function ($user)
            {
                return $user->getAllPermissions()->count();
            })
            ->addColumn('status_badge', function ($user)
            {
                $status = $user->status ?? 'active';
                $badgeClass = $status === 'active' ? 'badge-success' : 'badge-danger';
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($status) . '</span>';
            })
            ->addColumn('last_login', function ($user)
            {
                return $user->last_login_at ? $user->last_login_at->diffForHumans() : translate('never');
            })
            ->addColumn('created_at_formatted', function ($user)
            {
                return $user->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('actions', function ($user) {
                $actions = '';

                // Edit button
                $actions .= '<button type="button" data-id="' . $user->id . '" data-url="javascript:void(0)" class="btn btn-sm btn-primary edit-btn mx-1" title="' . translate('edit') . '"><i class="fas fa-edit"></i></button>';

                // Switch button
                $checked = ($user->status ?? 'active') === 'active' ? 'checked' : '';
                $actions .= '<div class="custom-control custom-switch d-inline-block mx-1"><input type="checkbox" class="custom-control-input switch-btn" id="switch-' . $user->id . '" data-id="' . $user->id . '" ' . $checked . '><label class="custom-control-label" for="switch-' . $user->id . '"></label></div>';

                // Delete button
                $actions .= '<button type="button" data-id="' . $user->id . '" data-url="javascript:void(0)" class="btn btn-sm btn-danger delete-btn mx-1" title="' . translate('delete') . '"><i class="fas fa-trash"></i></button>';

                // Add role management button
                $actions .= '<a href="javascript:void(0)" class="btn btn-sm.btn-info open-manage-roles-modal"
                           data-user-id="' . $user->id . '"
                           data-user-name="' . $user->name . '"
                           title="' . translate('manage_roles') . '">
                           <i class="fas fa-user-shield"></i>
                           </a>';

                return $actions;
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }
}
