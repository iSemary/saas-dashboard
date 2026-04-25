<?php

namespace Modules\HR\Application\UseCases\Attendance;

use Carbon\Carbon;
use Modules\HR\Application\DTOs\ApproveAttendanceData;
use Modules\HR\Domain\Entities\Attendance;
use Modules\HR\Infrastructure\Persistence\AttendanceRepositoryInterface;

class ApproveAttendanceUseCase
{
    public function __construct(
        protected AttendanceRepositoryInterface $attendanceRepository,
    ) {}

    public function execute(ApproveAttendanceData $data): Attendance
    {
        $attendance = $this->attendanceRepository->findOrFail($data->attendanceId);

        $updateData = [
            'is_approved' => $data->approved,
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ];

        if ($data->notes) {
            $updateData['notes'] = $attendance->notes . ' | Approval: ' . $data->notes;
        }

        return $this->attendanceRepository->update($data->attendanceId, $updateData);
    }
}
