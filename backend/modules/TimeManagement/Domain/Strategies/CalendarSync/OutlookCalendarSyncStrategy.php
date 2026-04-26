<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\CalendarSync;

class OutlookCalendarSyncStrategy implements CalendarSyncStrategyInterface
{
    public function getProviderName(): string
    {
        return 'outlook';
    }

    public function syncEvents(string $userId, array $tokens, array $options = []): array
    {
        return ['imported' => 0, 'updated' => 0, 'deleted' => 0];
    }

    public function pushEvent(string $userId, array $tokens, array $eventData): ?string
    {
        return null;
    }

    public function deleteEvent(string $userId, array $tokens, string $externalEventId): bool
    {
        return false;
    }
}
