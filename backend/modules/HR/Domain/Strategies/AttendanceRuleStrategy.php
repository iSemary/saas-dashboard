<?php

namespace Modules\HR\Domain\Strategies;

use Modules\HR\Domain\Entities\Attendance;
use Modules\HR\Domain\Entities\Shift;

interface AttendanceRuleStrategy
{
    public function validateCheckIn(Attendance $attendance, Shift $shift): bool;
    public function validateCheckOut(Attendance $attendance, Shift $shift): bool;
    public function calculateWorkingHours(Attendance $attendance): float;
    public function determineStatus(Attendance $attendance, Shift $shift): string;
}
