<?php

namespace Modules\HR\Application\UseCases\Leave;

use Carbon\Carbon;
use Modules\HR\Application\DTOs\ApproveLeaveData;
use Modules\HR\Domain\Entities\LeaveRequest;
use Modules\HR\Domain\ValueObjects\LeaveStatus;
use Modules\HR\Infrastructure\Persistence\LeaveRequestRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\LeaveBalanceRepositoryInterface;

class ApproveLeaveUseCase
{
    public function __construct(
        protected LeaveRequestRepositoryInterface $leaveRequestRepository,
        protected LeaveBalanceRepositoryInterface $leaveBalanceRepository,
    ) {}

    public function execute(ApproveLeaveData $data): LeaveRequest
    {
        $leaveRequest = $this->leaveRequestRepository->findOrFail($data->leaveRequestId);

        if ($leaveRequest->status !== LeaveStatus::PENDING->value) {
            throw new \RuntimeException('Only pending leave requests can be approved');
        }

        // Deduct from balance
        $year = Carbon::parse($leaveRequest->start_date)->year;
        $balance = $this->leaveBalanceRepository->getBalanceForEmployee(
            $leaveRequest->employee_id,
            $leaveRequest->leave_type_id,
            $year
        );

        if ($balance) {
            $this->leaveBalanceRepository->deductDays($balance->id, $leaveRequest->total_days);
        }

        $updateData = [
            'status' => LeaveStatus::APPROVED->value,
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ];

        return $this->leaveRequestRepository->update($data->leaveRequestId, $updateData);
    }
}
