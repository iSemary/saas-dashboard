<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\Notification;

use Modules\Auth\Entities\User;

/**
 * SMS notification strategy using the Notification module.
 */
class SmsNotificationStrategy implements NotificationStrategyInterface
{
    public function supports(string $channel): bool
    {
        return $channel === 'sms';
    }

    public function send(object $notifiable, string $message, array $context = []): bool
    {
        if (!$notifiable instanceof User) {
            return false;
        }

        // Get phone from user or profile
        $phone = $notifiable->phone ?? $notifiable->profile?->phone ?? null;
        if (empty($phone)) {
            return false;
        }

        try {
            // Use Notification module's SMS channel
            $notificationService = app(\Modules\Notification\Services\NotificationService::class);

            // Create notification with SMS channel
            $notificationService->createNotification([
                'user_id' => $notifiable->id,
                'title' => $context['title'] ?? 'CRM Alert',
                'message' => $message,
                'channels' => ['sms'],
                'sms_data' => [
                    'phone' => $phone,
                    'message' => $this->formatForSms($message, $context),
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
        return 'SMS';
    }

    public function isAvailable(): bool
    {
        // Check if Notification module has SMS capability configured
        return config('notification.sms.enabled', false) === true;
    }

    /**
     * Format message for SMS (truncate if too long, add short URL).
     */
    private function formatForSms(string $message, array $context): string
    {
        // SMS should be short - max 160 chars
        $maxLength = 160;

        if (strlen($message) > $maxLength) {
            $message = substr($message, 0, $maxLength - 3) . '...';
        }

        return $message;
    }
}
