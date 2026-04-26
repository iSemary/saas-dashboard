<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\UtilizationCalculation;

interface UtilizationCalculationStrategyInterface
{
    public function calculate(float $billableHours, float $totalHours): float;
    public function calculateForPeriod(string $userId, string $startDate, string $endDate): array;
}
