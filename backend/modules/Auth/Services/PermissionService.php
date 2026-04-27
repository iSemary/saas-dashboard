<?php

namespace Modules\Auth\Services;

use Modules\Auth\Repositories\PermissionInterface;
use Modules\Auth\Entities\Permission;

class PermissionService
{
    protected $repository;
    public $model;

    public function __construct(PermissionInterface $repository, Permission $permission)
    {
        $this->model = $permission;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function restore($id)
    {
        return $this->repository->restore($id);
    }
}

