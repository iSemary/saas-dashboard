<?php

namespace Modules\Auth\Services;

use Modules\Auth\Repositories\RoleInterface;
use Modules\Auth\Entities\Role;

class RoleService
{
    protected $repository;
    public $model;

    public function __construct(RoleInterface $repository, Role $role)
    {
        $this->model = $role;
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

