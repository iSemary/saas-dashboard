<?php

namespace Modules\HR\Infrastructure\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Infrastructure\Persistence\LeaveBalanceRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\LeaveTypeRepositoryInterface;

class YearEndCarryOverJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected LeaveBalanceRepositoryInterface $leaveBalanceRepository,
        protected LeaveTypeRepositoryInterface $leaveTypeRepository,
    ) {}

    public function handle(): void
    {
        $now = Carbon::now();
        $previousYear = $now->year - 1;
        $currentYear = $now->year;

        // Get all leave balances from previous year
        $balances = $this->leaveBalanceRepository->paginate(1000, ['year' => $previousYear]);

        foreach ($balances->items() as $balance) {
            $carryOverDays = min($balance->remaining, 5); // Max 5 days carry over
            
            if ($carryOverDays > 0) {
                // Check if current year balance exists
                $currentBalance = $this->leaveBalanceRepository->getBalanceForEmployee(
                    $balance->employee_id,
                    $balance->leave_type_id,
                    $currentYear
                );

                if ($currentBalance) {
                    $this->leaveBalanceRepository->update($currentBalance->id, [
                        'carried_over' => $carryOverDays,
                        'remaining' => $currentBalance->remaining + $carryOverDays,
                    ]);
                } else {
                    $this->leaveBalanceRepository->create([
                        'employee_id' => $balance->employee_id,
                        'leave_type_id' => $balance->leave_type_id,
                        'year' => $currentYear,
                        'allocated' => 0,
                        'accrued' => 0,
                        'used' => 0,
                        'carried_over' => $carryOverDays,
                        'remaining' => $carryOverDays,
                        'created_by' => null,
                    ]);
                }
            }
        }
    }
}
