<?php

namespace Modules\Email\Services;

use Modules\Email\Entities\EmailSubscriber;
use Modules\Email\Repositories\EmailSubscriberInterface;

class EmailSubscriberService
{
    protected $repository;
    public $model;

    public function __construct(EmailSubscriberInterface $repository, EmailSubscriber $emailSubscriber)
    {
        $this->model = $emailSubscriber;
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
