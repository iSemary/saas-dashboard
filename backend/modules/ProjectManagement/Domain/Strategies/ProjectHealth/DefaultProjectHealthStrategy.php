<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\ProjectHealth;

use Modules\ProjectManagement\Domain\ValueObjects\ProjectHealth;

class DefaultProjectHealthStrategy implements ProjectHealthStrategyInterface
{
    public function calculateScore(string $projectId): float
    {
        // Default: return 100 (on track). Real implementation queries tasks.
        return 100.0;
    }

    public function getHealthLabel(float $score): string
    {
        return ProjectHealth::fromScore($score)->value;
    }
}
