<?php

namespace Modules\Auth\Repositories\Tenant;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;

class TenantPermissionApiRepository implements TenantPermissionApiRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = Permission::query();
        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Permission
    {
        return Permission::create($data);
    }

    public function findOrFail(int $id): Permission
    {
        return Permission::findOrFail($id);
    }

    public function update(int $id, array $data): Permission
    {
        $permission = Permission::findOrFail($id);
        $permission->update($data);
        return $permission;
    }

    public function delete(int $id): bool
    {
        return Permission::findOrFail($id)->delete();
    }
}
