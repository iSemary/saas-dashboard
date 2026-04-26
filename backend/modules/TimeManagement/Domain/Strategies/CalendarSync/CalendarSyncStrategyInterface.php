<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\CalendarSync;

interface CalendarSyncStrategyInterface
{
    public function getProviderName(): string;
    public function syncEvents(string $userId, array $tokens, array $options = []): array;
    public function pushEvent(string $userId, array $tokens, array $eventData): ?string;
    public function deleteEvent(string $userId, array $tokens, string $externalEventId): bool;
}
