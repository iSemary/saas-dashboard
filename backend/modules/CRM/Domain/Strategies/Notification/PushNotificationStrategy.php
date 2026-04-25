<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\Notification;

use Modules\Auth\Entities\User;

/**
 * Push notification strategy using the Notification module.
 */
class PushNotificationStrategy implements NotificationStrategyInterface
{
    public function supports(string $channel): bool
    {
        return $channel === 'push';
    }

    public function send(object $notifiable, string $message, array $context = []): bool
    {
        if (!$notifiable instanceof User) {
            return false;
        }

        try {
            // Use Notification module's push channel
            $notificationService = app(\Modules\Notification\Services\NotificationService::class);

            $notificationService->createNotification([
                'user_id' => $notifiable->id,
                'title' => $context['title'] ?? 'CRM Notification',
                'message' => $message,
                'channels' => ['push'],
                'push_data' => [
                    'icon' => $context['icon'] ?? '/assets/icon.png',
                    'badge' => $context['badge'] ?? 1,
                    'tag' => $context['tag'] ?? 'crm-notification',
                    'requireInteraction' => $context['require_interaction'] ?? false,
                    'actions' => $context['actions'] ?? [],
                    'data' => [
                        'url' => $context['action_url'] ?? '/dashboard',
                        'model_type' => $context['model_type'] ?? null,
                        'model_id' => $context['model_id'] ?? null,
                    ],
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public function getChannelName(): string
    {
        return 'Push Notification';
    }

    public function isAvailable(): bool
    {
        // Check if Notification module has push capability configured
        return config('notification.push.enabled', false) === true;
    }
}
