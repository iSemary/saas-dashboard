<?php

namespace Modules\HR\Application\UseCases\Leave;

use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Domain\Entities\LeaveType;
use Modules\HR\Infrastructure\Persistence\LeaveBalanceRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;

class AccrueLeaveUseCase
{
    public function __construct(
        protected LeaveBalanceRepositoryInterface $leaveBalanceRepository,
        protected EmployeeRepositoryInterface $employeeRepository,
    ) {}

    public function execute(int $employeeId, int $leaveTypeId, int $year, float $days): void
    {
        $balance = $this->leaveBalanceRepository->getBalanceForEmployee($employeeId, $leaveTypeId, $year);

        if ($balance) {
            $this->leaveBalanceRepository->addDays($balance->id, $days);
        } else {
            // Create new balance record
            $this->leaveBalanceRepository->create([
                'employee_id' => $employeeId,
                'leave_type_id' => $leaveTypeId,
                'year' => $year,
                'allocated' => 0,
                'accrued' => $days,
                'used' => 0,
                'carried_over' => 0,
                'remaining' => $days,
                'created_by' => auth()->id(),
            ]);
        }
    }

    public function accrueForAllEmployees(int $leaveTypeId, int $year, float $days): int
    {
        $count = 0;
        $activeEmployees = $this->employeeRepository->getActiveEmployees();

        foreach ($activeEmployees as $employee) {
            $this->execute($employee['id'], $leaveTypeId, $year, $days);
            $count++;
        }

        return $count;
    }
}
