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

    public function list($filters = [])
    {
        $query = $this->model->where("user_id", auth()->id());

        // Apply filters
        if (isset($filters['status'])) {
            if ($filters['status'] === 'unread') {
                $query->unread();
            } elseif ($filters['status'] === 'read') {
                $query->read();
            }
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query
            ->orderByDesc("id")
            ->paginate($filters['per_page'] ?? 10)
            ->through(fn($item) => $item->toArray() + [
                'created_at_diff' => $item->created_at->diffForHumans(),
                'title' => $item->title,
                'body' => $item->body,
            ]);
    }
    
    public function markAllAsRead()
    {
        return $this->model->where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'seen_at' => now()
            ]);
    }

    public function markAsRead(int $id)
    {
        return $this->model->where("id", $id)
            ->where('user_id', auth()->id())
            ->update([
                'is_read' => true,
                'seen_at' => now()
            ]);
    }

    public function markAsUnread(int $id)
    {
        return $this->model->where("id", $id)
            ->where('user_id', auth()->id())
            ->update([
                'is_read' => false,
                'seen_at' => null
            ]);
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

    public function restore($id)
    {
        $row = $this->model->withTrashed()->find($id);
        if ($row) {
            $row->restore();
            return true;
        }
        return false;
    }

    public function getUnreadCount($userId = null)
    {
        $userId = $userId ?: auth()->id();
        return $this->model->where('user_id', $userId)->unread()->count();
    }

    public function getStats($userId = null)
    {
        $userId = $userId ?: auth()->id();
        $query = $this->model->where('user_id', $userId);
        
        return [
            'total' => $query->count(),
            'unread' => $query->unread()->count(),
            'read' => $query->read()->count(),
            'by_type' => $query->groupBy('type')->selectRaw('type, count(*) as count')->pluck('count', 'type')->toArray()
        ];
    }

    public function create($data)
    {
        return $this->model->create($data);
    }
}
