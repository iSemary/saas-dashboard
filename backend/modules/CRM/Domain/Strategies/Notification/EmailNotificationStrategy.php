<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\Notification;

use Modules\Auth\Entities\User;

/**
 * Email notification strategy using the Email module.
 */
class EmailNotificationStrategy implements NotificationStrategyInterface
{
    public function supports(string $channel): bool
    {
        return $channel === 'email';
    }

    public function send(object $notifiable, string $message, array $context = []): bool
    {
        if (!$notifiable instanceof User) {
            return false;
        }

        $email = $notifiable->email;
        if (empty($email)) {
            return false;
        }

        $subject = $context['title'] ?? 'CRM Notification';
        $template = $context['template'] ?? 'generic';

        try {
            // Use Email module integration
            $integration = app(\Modules\CRM\Infrastructure\Integrations\EmailIntegration::class);

            $integration->send([
                'to' => $email,
                'subject' => $subject,
                'body' => $message,
                'template' => $template,
                'track_opens' => true,
            ]);

            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public function getChannelName(): string
    {
        return 'Email';
    }

    public function isAvailable(): bool
    {
        // Check if Email module is enabled and configured
        return class_exists(\Modules\Email\Services\EmailService::class);
    }
}
