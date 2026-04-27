<?php

namespace Modules\Auth\Repositories;

use App\Helpers\TableHelper;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;
class RoleRepository implements RoleInterface
{
    protected $model;

    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        $permissions = $data['permissions'] ?? [];
        $permissionGroups = $data['permission_groups'] ?? [];
        unset($data['permissions'], $data['permission_groups']);

        $role = $this->model->create($data);

        if ($role) {
            // Sync permission groups
            if (!empty($permissionGroups)) {
                $role->permissionGroups()->sync($permissionGroups);
                
                // Get all permissions from assigned groups
                $groupPermissions = Permission::whereHas('permissionGroups', function($q) use ($permissionGroups) {
                    $q->whereIn('permission_groups.id', $permissionGroups);
                })->pluck('id')->toArray();
                
                // Merge with individually assigned permissions
                $permissions = array_unique(array_merge($permissions, $groupPermissions));
            }
            
            // Sync all permissions (from groups + individual)
            if (!empty($permissions)) {
                $role->permissions()->sync($permissions);
            }
        }

        return $role;
    }

    public function update($id, array $data)
    {
        $permissions = $data['permissions'] ?? [];
        $permissionGroups = $data['permission_groups'] ?? [];
        unset($data['permissions'], $data['permission_groups']);

        $role = $this->model->find($id);

        if ($role) {
            $role->update($data);
            
            // Sync permission groups
            if (isset($permissionGroups)) {
                $role->permissionGroups()->sync($permissionGroups);
                
                // Get all permissions from assigned groups
                $groupPermissions = Permission::whereHas('permissionGroups', function($q) use ($permissionGroups) {
                    $q->whereIn('permission_groups.id', $permissionGroups);
                })->pluck('id')->toArray();
                
                // Merge with individually assigned permissions
                $permissions = array_unique(array_merge($permissions, $groupPermissions));
            }
            
            // Sync all permissions (from groups + individual)
            if (isset($permissions)) {
                $role->permissions()->sync($permissions);
            }
            
            return $role;
        }

        return null;
    }

    public function delete($id)
    {
        $row = $this->model->where("id", $id);
        if ($row->first()) {
            $row->update(['deleted_at' => now()]);
            return true;
        }
        return false;
    }

    public function restore($id)
    {
        $row = $this->model->withTrashed()->where("id", $id);
        if ($row->first()) {
            $row->update(['deleted_at' => null]);
            return true;
        }
        return false;
    }
}
