<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\Notification;

/**
 * Interface for notification channel strategies.
 */
interface NotificationStrategyInterface
{
    /**
     * Check if this strategy supports the given channel.
     */
    public function supports(string $channel): bool;

    /**
     * Send a notification.
     *
     * @param object $notifiable The entity to notify (usually a User)
     * @param string $message The notification message
     * @param array $context Additional context (title, action URL, etc.)
     * @return bool True if notification was sent successfully
     */
    public function send(object $notifiable, string $message, array $context = []): bool;

    /**
     * Get the channel name.
     */
    public function getChannelName(): string;

    /**
     * Check if the channel is configured and available.
     */
    public function isAvailable(): bool;
}
