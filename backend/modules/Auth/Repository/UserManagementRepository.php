<?php

namespace Modules\Auth\Repository;

use Modules\Auth\Entities\User;
use Modules\Auth\Repository\UserManagementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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
}
