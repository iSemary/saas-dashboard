<?php

namespace Modules\HR\Application\DTOs;

readonly class ApproveAttendanceData
{
    public function __construct(
        public int $attendanceId,
        public bool $approved,
        public ?string $notes,
    ) {}
}
