<?php

namespace Modules\Auth\Repositories\Tenant;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class TenantUserApiRepository implements TenantUserApiRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = User::query();
        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return User::create($data);
    }

    public function findOrFail(int $id): User
    {
        return User::findOrFail($id);
    }

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return $user;
    }

    public function delete(int $id): bool
    {
        return User::findOrFail($id)->delete();
    }

    public function assignRoles(int $userId, array $roleIds): User
    {
        $user = User::findOrFail($userId);
        $user->syncRoles($roleIds);
        return $user;
    }
}
