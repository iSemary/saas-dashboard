<?php

namespace Modules\Email\Services;

use Modules\Email\Entities\EmailRecipient;
use Modules\Email\Repositories\EmailRecipientInterface;

class EmailRecipientService
{
    protected $repository;
    public $model;

    public function __construct(EmailRecipientInterface $repository, EmailRecipient $emailRecipient)
    {
        $this->model = $emailRecipient;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function count()
    {
        return $this->repository->count();
    }

    public function getPaginated()
    {
        return $this->repository->getPaginated();
    }

    public function getDataTables()
    {
        return $this->repository->datatables();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function getByEmail($email)
    {
        return $this->repository->getByEmail($email);
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
