<?php

namespace Modules\Auth\Repositories\Tenant;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class TenantRoleApiRepository implements TenantRoleApiRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = Role::query();
        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function findOrFail(int $id): Role
    {
        return Role::findOrFail($id);
    }

    public function update(int $id, array $data): Role
    {
        $role = Role::findOrFail($id);
        $role->update($data);
        return $role;
    }

    public function delete(int $id): bool
    {
        return Role::findOrFail($id)->delete();
    }
}
