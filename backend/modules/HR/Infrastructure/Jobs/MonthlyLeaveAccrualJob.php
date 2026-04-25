<?php

namespace Modules\HR\Infrastructure\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Application\UseCases\Leave\AccrueLeaveUseCase;
use Modules\HR\Infrastructure\Persistence\LeaveTypeRepositoryInterface;

class MonthlyLeaveAccrualJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected AccrueLeaveUseCase $accrueLeaveUseCase,
        protected LeaveTypeRepositoryInterface $leaveTypeRepository,
    ) {}

    public function handle(): void
    {
        $now = Carbon::now();
        $year = $now->year;
        
        // Get all active leave types with monthly accrual
        $leaveTypes = $this->leaveTypeRepository->getActiveLeaveTypes();
        
        foreach ($leaveTypes as $leaveType) {
            // Skip if not monthly accrual (would need leave policies for complex logic)
            // For now, simple monthly accrual
            $monthlyAccrualDays = $this->calculateMonthlyAccrual($leaveType);
            
            if ($monthlyAccrualDays > 0) {
                $this->accrueLeaveUseCase->accrueForAllEmployees(
                    $leaveType['id'],
                    $year,
                    $monthlyAccrualDays
                );
            }
        }
    }

    private function calculateMonthlyAccrual(array $leaveType): float
    {
        // Default: if leave type allows accrual, calculate monthly amount
        // This would typically come from a LeavePolicy configuration
        // For now, simple calculation: 1.67 days per month (20 days annual)
        $annualDays = 20;
        return round($annualDays / 12, 2);
    }
}
