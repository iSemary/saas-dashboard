<?php

namespace Modules\Notification\Services;

use Modules\Notification\Repositories\NotificationChannelInterface;
use NotificationChannels\WebPush\PushSubscription;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Support\Facades\Notification;
use Modules\Auth\Entities\User;

class PushSubscriptionService
{
    protected $channelRepository;

    public function __construct(NotificationChannelInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    /**
     * Subscribe user to push notifications
     */
    public function subscribe($userId, $subscriptionData)
    {
        return $this->channelRepository->createOrUpdateChannel(
            $userId,
            'push',
            $subscriptionData
        );
    }

    /**
     * Unsubscribe user from push notifications
     */
    public function unsubscribe($userId)
    {
        return $this->channelRepository->deactivateChannel($userId, 'push');
    }

    /**
     * Get user's push subscription
     */
    public function getSubscription($userId)
    {
        $channels = $this->channelRepository->getActiveChannels($userId, 'push');
        return $channels->first();
    }

    /**
     * Send test push notification
     */
    public function sendTestNotification($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            throw new \Exception('User not found');
        }

        $subscription = $this->getSubscription($userId);
        if (!$subscription) {
            throw new \Exception('No active push subscription found');
        }

        // Create a test notification
        $notification = new \Modules\Notification\Notifications\TestPushNotification();
        
        // Send the notification
        $user->notify($notification);

        // Mark channel as used
        $subscription->markAsUsed();

        return true;
    }

    /**
     * Convert our subscription data to WebPush format
     */
    public function convertToPushSubscription($subscriptionData)
    {
        return new PushSubscription(
            $subscriptionData['endpoint'],
            $subscriptionData['keys']['p256dh'] ?? null,
            $subscriptionData['keys']['auth'] ?? null
        );
    }
}
