<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\Scheduling;

interface SchedulingStrategyInterface
{
    public function calculateEndDate(string $projectId): ?\DateTimeInterface;
    public function detectConflicts(string $projectId): array;
}
