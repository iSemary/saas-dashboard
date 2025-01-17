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

    public function list()
    {
        return $this->repository->list();
    }

    public function markAllAsRead()
    {
        return $this->repository->markAllAsRead();
    }

    public function markAsRead($id)
    {
        return $this->repository->markAsRead($id);
    }

    public function markAsUnread($id)
    {
        return $this->repository->markAsUnread($id);
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

