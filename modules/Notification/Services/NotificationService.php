<?php

namespace Modules\Notification\Services;

use Modules\Notification\Entities\Notification;
use Modules\Notification\Repositories\NotificationInterface;

class NotificationService
{
    protected $repository;
    public $model;

    public function __construct(NotificationInterface $repository, Notification $notification)
    {
        $this->model = $notification;
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
}
