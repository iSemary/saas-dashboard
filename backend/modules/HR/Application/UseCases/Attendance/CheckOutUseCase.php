<?php

namespace Modules\HR\Application\UseCases\Attendance;

use Carbon\Carbon;
use Modules\HR\Application\DTOs\CheckOutData;
use Modules\HR\Domain\Entities\Attendance;
use Modules\HR\Infrastructure\Persistence\AttendanceRepositoryInterface;

class CheckOutUseCase
{
    public function __construct(
        protected AttendanceRepositoryInterface $attendanceRepository,
    ) {}

    public function execute(CheckOutData $data): Attendance
    {
        $attendance = $this->attendanceRepository->findOrFail($data->attendanceId);
        
        if ($attendance->check_out) {
            throw new \RuntimeException(translate('message.operation_failed'));
        }

        if (!$attendance->check_in) {
            throw new \RuntimeException(translate('message.operation_failed'));
        }

        $checkOut = Carbon::now();
        $checkIn = Carbon::parse($attendance->check_in);
        
        // Calculate total hours
        $totalHours = $checkIn->diffInMinutes($checkOut) / 60;
        
        // Calculate break duration if applicable
        $breakDuration = 0;
        if ($attendance->break_start && $attendance->break_end) {
            $breakDuration = Carbon::parse($attendance->break_start)
                ->diffInMinutes(Carbon::parse($attendance->break_end)) / 60;
        }
        
        $workingHours = $totalHours - $breakDuration;
        
        // Calculate overtime (assuming 8 hours standard)
        $overtimeHours = max(0, $workingHours - 8);

        $updateData = [
            'check_out' => $checkOut,
            'total_hours' => round($workingHours, 2),
            'break_duration' => round($breakDuration, 2),
            'overtime_hours' => round($overtimeHours, 2),
            'notes' => $data->notes ? $attendance->notes . ' | ' . $data->notes : $attendance->notes,
        ];

        return $this->attendanceRepository->update($data->attendanceId, $updateData);
    }
}
