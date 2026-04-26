<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\UtilizationCalculation;

class DefaultUtilizationCalculationStrategy implements UtilizationCalculationStrategyInterface
{
    public function calculate(float $billableHours, float $totalHours): float
    {
        if ($totalHours <= 0) {
            return 0.0;
        }
        return round(($billableHours / $totalHours) * 100, 2);
    }

    public function calculateForPeriod(string $userId, string $startDate, string $endDate): array
    {
        return [
            'billable_hours' => 0,
            'total_hours' => 0,
            'utilization' => 0.0,
        ];
    }
}
