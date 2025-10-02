<?php

namespace Modules\Notification\Repositories;

use Modules\Notification\Entities\NotificationChannel;

class NotificationChannelRepository implements NotificationChannelInterface
{
    protected $model;

    public function __construct(NotificationChannel $notificationChannel)
    {
        $this->model = $notificationChannel;
    }

    public function getUserChannels($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function getActiveChannels($userId, $type = null)
    {
        $query = $this->model->where('user_id', $userId)->active();
        
        if ($type) {
            $query->byType($type);
        }
        
        return $query->get();
    }

    public function createOrUpdateChannel($userId, $type, $subscriptionData)
    {
        return $this->model->updateOrCreate(
            [
                'user_id' => $userId,
                'channel_type' => $type,
            ],
            [
                'subscription_data' => $subscriptionData,
                'is_active' => true,
                'subscribed_at' => now(),
            ]
        );
    }

    public function deactivateChannel($userId, $type)
    {
        return $this->model->where('user_id', $userId)
            ->where('channel_type', $type)
            ->update(['is_active' => false]);
    }

    public function activateChannel($userId, $type)
    {
        return $this->model->where('user_id', $userId)
            ->where('channel_type', $type)
            ->update(['is_active' => true]);
    }

    public function deleteChannel($userId, $type)
    {
        return $this->model->where('user_id', $userId)
            ->where('channel_type', $type)
            ->delete();
    }
}
