<?php

namespace Modules\HR\Infrastructure\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Domain\ValueObjects\AttendanceStatus;
use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\AttendanceRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\WorkScheduleRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\HolidayRepositoryInterface;

class MarkAbsentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected EmployeeRepositoryInterface $employeeRepository,
        protected AttendanceRepositoryInterface $attendanceRepository,
        protected WorkScheduleRepositoryInterface $workScheduleRepository,
        protected HolidayRepositoryInterface $holidayRepository,
    ) {}

    public function handle(): void
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Get all active employees
        $employees = $this->employeeRepository->getActiveEmployees();

        foreach ($employees as $employee) {
            // Check if employee has a work schedule for yesterday
            $schedule = $this->workScheduleRepository->getCurrentScheduleForEmployee($employee['id']);
            
            if (!$schedule) {
                continue;
            }

            // Check if yesterday was a working day
            $dayOfWeek = strtolower($yesterday->format('l'));
            $workingDays = $schedule->shift->working_days ?? [];
            
            if (!in_array($dayOfWeek, $workingDays)) {
                continue;
            }

            // Check if yesterday was a holiday
            if ($this->holidayRepository->isHoliday($yesterday->toDateString())) {
                continue;
            }

            // Check if attendance already recorded
            $existing = $this->attendanceRepository->getTodayAttendance($employee['id']);
            
            if ($existing) {
                continue;
            }

            // Mark as absent
            $this->attendanceRepository->create([
                'employee_id' => $employee['id'],
                'date' => $yesterday,
                'status' => AttendanceStatus::ABSENT->value,
                'is_approved' => true,
                'created_by' => null, // System generated
            ]);
        }
    }
}
