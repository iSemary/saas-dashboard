<?php

namespace Modules\HR\Application\UseCases\Leave;

use Modules\HR\Domain\Entities\LeaveRequest;
use Modules\HR\Domain\ValueObjects\LeaveStatus;
use Modules\HR\Infrastructure\Persistence\LeaveRequestRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\LeaveBalanceRepositoryInterface;
use Carbon\Carbon;

class CancelLeaveUseCase
{
    public function __construct(
        protected LeaveRequestRepositoryInterface $leaveRequestRepository,
        protected LeaveBalanceRepositoryInterface $leaveBalanceRepository,
    ) {}

    public function execute(int $leaveRequestId): LeaveRequest
    {
        $leaveRequest = $this->leaveRequestRepository->findOrFail($leaveRequestId);

        // Only pending or approved requests can be cancelled
        if (!in_array($leaveRequest->status, [LeaveStatus::PENDING->value, LeaveStatus::APPROVED->value])) {
            throw new \RuntimeException(translate('message.operation_failed'));
        }

        // If already approved, restore the balance
        if ($leaveRequest->status === LeaveStatus::APPROVED->value) {
            $year = Carbon::parse($leaveRequest->start_date)->year;
            $balance = $this->leaveBalanceRepository->getBalanceForEmployee(
                $leaveRequest->employee_id,
                $leaveRequest->leave_type_id,
                $year
            );

            if ($balance) {
                $this->leaveBalanceRepository->addDays($balance->id, $leaveRequest->total_days);
            }
        }

        return $this->leaveRequestRepository->update($leaveRequestId, [
            'status' => LeaveStatus::CANCELLED->value,
        ]);
    }
}
