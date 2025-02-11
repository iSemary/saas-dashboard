<?php

namespace Modules\Email\Services;

use Modules\Email\Entities\EmailGroup;
use Modules\Email\Repositories\EmailGroupInterface;

class EmailGroupService
{
    protected $repository;
    public $model;

    public function __construct(EmailGroupInterface $repository, EmailGroup $emailGroup)
    {
        $this->model = $emailGroup;
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

    public function getPaginated()
    {
        return $this->repository->getPaginated();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function getRecipientsByIds($ids)
    {
        return $this->repository->getRecipientsByIds($ids);
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
