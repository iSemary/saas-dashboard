<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\ProjectHealth;

interface ProjectHealthStrategyInterface
{
    public function calculateScore(string $projectId): float;
    public function getHealthLabel(float $score): string;
}
