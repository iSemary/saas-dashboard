<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Notification;

use Modules\Survey\Domain\Entities\SurveyResponse;

interface NotificationStrategyInterface
{
    /**
     * Check if this strategy supports the given channel.
     */
    public function supports(string $channel): bool;

    /**
     * Send a notification.
     *
     * @return array{success: bool, message: string, notification_id?: string}
     */
    public function send(
        string $recipient,
        string $subject,
        string $message,
        array $data = [],
        array $options = []
    ): array;

    /**
     * Send bulk notifications.
     *
     * @param array<int, string> $recipients
     * @return array{success: bool, sent_count: int, failed_count: int}
     */
    public function sendBulk(
        array $recipients,
        string $subject,
        string $message,
        array $data = [],
        array $options = []
    ): array;

    /**
     * Get the channel name.
     */
    public function getChannel(): string;

    /**
     * Get the channel label.
     */
    public function getLabel(): string;

    /**
     * Check if this channel is configured and available.
     */
    public function isAvailable(): bool;
}
