<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\Notification;

class DefaultNotificationStrategy implements NotificationStrategyInterface
{
    public function notify(string $userId, string $type, array $payload): void
    {
        // Default: no-op. Real implementation uses Notification module.
    }
}
