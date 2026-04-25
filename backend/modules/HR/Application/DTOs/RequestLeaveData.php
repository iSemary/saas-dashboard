<?php

namespace Modules\HR\Application\DTOs;

readonly class RequestLeaveData
{
    public function __construct(
        public int $employeeId,
        public int $leaveTypeId,
        public string $startDate,
        public string $endDate,
        public bool $isHalfDay,
        public ?string $halfDaySession,
        public ?string $reason,
    ) {}

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employeeId,
            'leave_type_id' => $this->leaveTypeId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'is_half_day' => $this->isHalfDay,
            'half_day_session' => $this->halfDaySession,
            'reason' => $this->reason,
        ];
    }
}
