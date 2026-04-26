<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\ConflictDetection;

interface ConflictDetectionStrategyInterface
{
    public function detectConflicts(string $userId, string $startsAt, string $endsAt, ?string $excludeEventId = null): array;
}
