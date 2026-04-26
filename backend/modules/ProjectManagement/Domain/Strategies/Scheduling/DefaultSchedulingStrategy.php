<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\Scheduling;

class DefaultSchedulingStrategy implements SchedulingStrategyInterface
{
    public function calculateEndDate(string $projectId): ?\DateTimeInterface
    {
        return null;
    }

    public function detectConflicts(string $projectId): array
    {
        return [];
    }
}
