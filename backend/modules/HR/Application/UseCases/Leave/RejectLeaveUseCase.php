<?php

namespace Modules\HR\Application\UseCases\Leave;

use Carbon\Carbon;
use Modules\HR\Application\DTOs\RejectLeaveData;
use Modules\HR\Domain\Entities\LeaveRequest;
use Modules\HR\Domain\ValueObjects\LeaveStatus;
use Modules\HR\Infrastructure\Persistence\LeaveRequestRepositoryInterface;

class RejectLeaveUseCase
{
    public function __construct(
        protected LeaveRequestRepositoryInterface $leaveRequestRepository,
    ) {}

    public function execute(RejectLeaveData $data): LeaveRequest
    {
        $leaveRequest = $this->leaveRequestRepository->findOrFail($data->leaveRequestId);

        if ($leaveRequest->status !== LeaveStatus::PENDING->value) {
            throw new \RuntimeException(translate('message.operation_failed'));
        }

        $updateData = [
            'status' => LeaveStatus::REJECTED->value,
            'rejection_reason' => $data->reason,
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ];

        return $this->leaveRequestRepository->update($data->leaveRequestId, $updateData);
    }
}
