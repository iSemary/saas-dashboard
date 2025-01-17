<?php

namespace Modules\Notification\Repositories;

use Modules\Notification\Entities\Notification;

class NotificationRepository implements NotificationInterface
{
    protected $model;

    public function __construct(Notification $notification)
    {
        $this->model = $notification;
    }

    public function list()
    {
        return $this->model->where("user_id", auth()->id())->orderByDesc("id")->paginate(10);
    }

    public function markAllAsRead()
    {
        return $this->model->whereNull("seen_at")->where('user_id', auth()->id())->update(['seen_at' => now()]);
    }

    public function markAsRead(int $id)
    {
        return $this->model->whereNull("seen_at")->where("id", $id)->where('user_id', auth()->id())->update(['seen_at' => now()]);
    }

    public function markAsUnread(int $id)
    {
        return $this->model->where("id", $id)->where('user_id', auth()->id())->update(['seen_at' => null]);
    }

    public function delete($id)
    {
        $row = $this->model->where("user_id", auth()->id)->find($id);
        if ($row) {
            $row->delete();
            return true;
        }
        return false;
    }
}
