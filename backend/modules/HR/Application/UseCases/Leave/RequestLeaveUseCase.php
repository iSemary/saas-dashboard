<?php

namespace Modules\HR\Application\UseCases\Leave;

use Carbon\Carbon;
use Modules\HR\Application\DTOs\RequestLeaveData;
use Modules\HR\Domain\Entities\LeaveRequest;
use Modules\HR\Domain\ValueObjects\LeaveStatus;
use Modules\HR\Infrastructure\Persistence\LeaveRequestRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\LeaveBalanceRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\LeaveTypeRepositoryInterface;

class RequestLeaveUseCase
{
    public function __construct(
        protected LeaveRequestRepositoryInterface $leaveRequestRepository,
        protected LeaveBalanceRepositoryInterface $leaveBalanceRepository,
        protected LeaveTypeRepositoryInterface $leaveTypeRepository,
    ) {}

    public function execute(RequestLeaveData $data): LeaveRequest
    {
        // Check for overlapping leave requests
        if ($this->leaveRequestRepository->hasOverlappingLeave(
            $data->employeeId,
            $data->startDate,
            $data->endDate
        )) {
            throw new \RuntimeException(translate('message.operation_failed'));
        }

        $leaveType = $this->leaveTypeRepository->findOrFail($data->leaveTypeId);

        // Calculate total days
        $start = Carbon::parse($data->startDate);
        $end = Carbon::parse($data->endDate);
        $totalDays = $data->isHalfDay ? 0.5 : $start->diffInDays($end) + 1;

        // Check leave balance if not allowing negative
        if (!$leaveType->allow_negative_balance) {
            $year = now()->year;
            $balance = $this->leaveBalanceRepository->getBalanceForEmployee(
                $data->employeeId,
                $data->leaveTypeId,
                $year
            );

            if (!$balance || $balance->remaining < $totalDays) {
                throw new \RuntimeException(translate('message.operation_failed'));
            }
        }

        // Check minimum notice days
        if ($leaveType->min_notice_days) {
            $minStartDate = now()->addDays($leaveType->min_notice_days);
            if ($start->lessThan($minStartDate)) {
                throw new \RuntimeException(
                    "Must request at least {$leaveType->min_notice_days} days in advance"
                );
            }
        }

        // Check max consecutive days
        if ($leaveType->max_consecutive_days && $totalDays > $leaveType->max_consecutive_days) {
            throw new \RuntimeException(
                "Maximum {$leaveType->max_consecutive_days} consecutive days allowed"
            );
        }

        // Check if half-day is allowed
        if ($data->isHalfDay && !$leaveType->allow_half_day) {
            throw new \RuntimeException(translate('message.operation_failed'));
        }

        $leaveRequestData = [
            'employee_id' => $data->employeeId,
            'leave_type_id' => $data->leaveTypeId,
            'start_date' => $data->startDate,
            'end_date' => $data->endDate,
            'total_days' => $totalDays,
            'is_half_day' => $data->isHalfDay,
            'half_day_session' => $data->halfDaySession,
            'reason' => $data->reason,
            'status' => LeaveStatus::PENDING->value,
            'created_by' => auth()->id(),
        ];

        return $this->leaveRequestRepository->create($leaveRequestData);
    }
}
