<?php

namespace Modules\Subscription\Services;

use Carbon\Carbon;
use Modules\Subscription\DTOs\ProrationResult;

class ProrationCalculator
{
    /**
     * Calculate proration for a plan/module change.
     *
     * @param float $currentPrice The current subscription price
     * @param float $newPrice The new subscription price
     * @param Carbon $periodStart Current billing period start
     * @param Carbon $periodEnd Current billing period end
     * @param Carbon $changeDate Date of change (defaults to now)
     * @param string|null $description Description of the change
     * @return ProrationResult
     */
    public function calculate(
        float $currentPrice,
        float $newPrice,
        Carbon $periodStart,
        Carbon $periodEnd,
        ?Carbon $changeDate = null,
        ?string $description = null
    ): ProrationResult {
        $changeDate = $changeDate ?? Carbon::now();
        
        $totalDays = $periodStart->diffInDays($periodEnd) + 1;
        $daysUsed = $periodStart->diffInDays($changeDate);
        $daysRemaining = max(0, $totalDays - $daysUsed);
        
        if ($totalDays <= 0) {
            $totalDays = 1;
        }
        
        $prorationFactor = $daysRemaining / $totalDays;
        
        // Credit for unused time on current plan
        $creditAmount = round($currentPrice * $prorationFactor, 2);
        
        // Charge for remaining time on new plan
        $debitAmount = round($newPrice * $prorationFactor, 2);
        
        $netAmount = round($debitAmount - $creditAmount, 2);
        
        return new ProrationResult(
            credit_amount: $creditAmount,
            debit_amount: $debitAmount,
            net_amount: $netAmount,
            days_remaining: (int) $daysRemaining,
            days_in_period: (int) $totalDays,
            proration_factor: $prorationFactor,
            period_start: $periodStart->toDateString(),
            period_end: $periodEnd->toDateString(),
            description: $description ?? "Prorated change from {$currentPrice} to {$newPrice}",
            metadata: [
                'days_used' => $daysUsed,
                'change_date' => $changeDate->toDateTimeString(),
            ]
        );
    }

    /**
     * Calculate remaining value of a subscription.
     *
     * @param float $price
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @param Carbon|null $asOfDate
     * @return float
     */
    public function calculateRemainingValue(
        float $price,
        Carbon $periodStart,
        Carbon $periodEnd,
        ?Carbon $asOfDate = null
    ): float {
        $asOfDate = $asOfDate ?? Carbon::now();
        
        $totalDays = $periodStart->diffInDays($periodEnd) + 1;
        $daysUsed = $periodStart->diffInDays($asOfDate);
        $daysRemaining = max(0, $totalDays - $daysUsed);
        
        if ($totalDays <= 0) {
            return 0;
        }
        
        return round($price * ($daysRemaining / $totalDays), 2);
    }

    /**
     * Calculate charge for partial period.
     *
     * @param float $monthlyPrice
     * @param int $days
     * @param string $billingCycle
     * @return float
     */
    public function calculatePartialCharge(
        float $monthlyPrice,
        int $days,
        string $billingCycle = 'monthly'
    ): float {
        $cycleDays = match($billingCycle) {
            'monthly' => 30,
            'quarterly' => 90,
            'semi_annually' => 180,
            'annually' => 365,
            'biennially' => 730,
            'triennially' => 1095,
            default => 30,
        };
        
        return round($monthlyPrice * ($days / $cycleDays), 2);
    }
}
