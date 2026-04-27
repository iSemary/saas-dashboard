<?php

namespace Modules\Auth\Repositories;

use App\Helpers\TableHelper;
use Modules\Auth\Entities\PermissionGroup;
class PermissionGroupRepository implements PermissionGroupInterface
{
    protected $model;

    public function __construct(PermissionGroup $permissionGroup)
    {
        $this->model = $permissionGroup;
    }

    public function all()
    {
        return $this->model->with('permissions')->withCount('permissions')->get();
    }

    public function find($id)
    {
        return $this->model->with('permissions')->withCount('permissions')->find($id);
    }

    public function create(array $data)
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $permissionGroup = $this->model->create($data);

        if ($permissionGroup && !empty($permissions)) {
            $permissionGroup->permissions()->sync($permissions);
        }

        return $permissionGroup;
    }

    public function update($id, array $data)
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $permissionGroup = $this->model->find($id);

        if ($permissionGroup) {
            $permissionGroup->update($data);
            
            if (isset($permissions)) {
                $permissionGroup->permissions()->sync($permissions);
            }
            
            return $permissionGroup;
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
