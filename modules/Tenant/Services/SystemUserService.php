<?php

namespace Modules\Tenant\Services;

use Modules\Auth\Entities\User;
use Modules\Tenant\Repositories\SystemUserInterface;

class SystemUserService
{
    protected $repository;
    public $model;

    public function __construct(SystemUserInterface $repository, User $user)
    {
        $this->model = $user;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getDataTables()
    {
        return $this->repository->datatables();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function checkEmail($email, $id = null)
    {
        return $this->repository->checkEmail($email, $id);
    }
}
