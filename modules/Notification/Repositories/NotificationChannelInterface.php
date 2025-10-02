<?php

namespace Modules\Notification\Repositories;

interface NotificationChannelInterface
{
    public function getUserChannels($userId);
    public function getActiveChannels($userId, $type = null);
    public function createOrUpdateChannel($userId, $type, $subscriptionData);
    public function deactivateChannel($userId, $type);
    public function activateChannel($userId, $type);
    public function deleteChannel($userId, $type);
}
