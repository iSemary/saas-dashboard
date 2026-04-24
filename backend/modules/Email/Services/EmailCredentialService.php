<?php

namespace Modules\Email\Services;

use Modules\Email\Entities\EmailCredential;
use Modules\Email\Repositories\EmailCredentialInterface;

class EmailCredentialService
{
    protected $repository;
    public $model;

    public function __construct(EmailCredentialInterface $repository, EmailCredential $emailCredential)
    {
        $this->model = $emailCredential;
        $this->repository = $repository;
    }

    public function getAll(array $conditions = [])
    {
        return $this->repository->all($conditions);
    }

    public function getDataTables()
    {
        return $this->repository->datatables();
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
