<?php

namespace Modules\Auth\Repositories;

use App\Helpers\TableHelper;
use Modules\Auth\Entities\Permission;
class PermissionRepository implements PermissionInterface
{
    protected $model;

    public function __construct(Permission $permission)
    {
        $this->model = $permission;
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
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->update($data);
            return $row;
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
