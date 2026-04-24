<?php

namespace Modules\Notification\Repositories;

interface NotificationInterface
{
    public function list();
    public function markAllAsRead();
    public function markAsRead(int $id);
    public function markAsUnread(int $id);
    public function delete($id);
    public function restore($id);
}

