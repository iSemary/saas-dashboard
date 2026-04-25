<?php

namespace Modules\HR\Application\UseCases\Attendance;

use Carbon\Carbon;
use Modules\HR\Application\DTOs\CheckInData;
use Modules\HR\Domain\Entities\Attendance;
use Modules\HR\Domain\ValueObjects\AttendanceSource;
use Modules\HR\Domain\ValueObjects\AttendanceStatus;
use Modules\HR\Infrastructure\Persistence\AttendanceRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\WorkScheduleRepositoryInterface;

class CheckInUseCase
{
    public function __construct(
        protected AttendanceRepositoryInterface $attendanceRepository,
        protected WorkScheduleRepositoryInterface $workScheduleRepository,
    ) {}

    public function execute(CheckInData $data): Attendance
    {
        $today = Carbon::today();
        
        // Check if already checked in today
        $existing = $this->attendanceRepository->getTodayAttendance($data->employeeId);
        if ($existing && $existing->check_in) {
            throw new \RuntimeException('Already checked in for today');
        }

        // Get employee's current schedule
        $schedule = $this->workScheduleRepository->getCurrentScheduleForEmployee($data->employeeId);
        
        // Determine status based on shift start time
        $status = AttendanceStatus::PRESENT;
        $lateMinutes = 0;
        
        if ($schedule && $schedule->shift) {
            $shiftStart = Carbon::parse($schedule->shift->start_time);
            $now = Carbon::now();
            $graceMinutes = $schedule->shift->grace_minutes ?? 0;
            
            if ($now->greaterThan($shiftStart->copy()->addMinutes($graceMinutes))) {
                $status = AttendanceStatus::LATE;
                $lateMinutes = $now->diffInMinutes($shiftStart);
            }
        }

        $attendanceData = [
            'employee_id' => $data->employeeId,
            'date' => $today,
            'check_in' => Carbon::now(),
            'status' => $status->value,
            'source' => AttendanceSource::WEB->value,
            'ip_address' => $data->ipAddress,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'notes' => $data->notes,
            'is_approved' => true,
            'created_by' => auth()->id(),
        ];

        return $this->attendanceRepository->create($attendanceData);
    }
}
