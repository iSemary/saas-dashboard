<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\Notification;

interface NotificationStrategyInterface
{
    public function notify(string $userId, string $type, array $payload): void;
}
