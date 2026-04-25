<?php

namespace Modules\HR\Domain\Strategies;

use Modules\HR\Domain\Entities\Attendance;
use Modules\HR\Domain\Entities\Shift;

interface OvertimeCalculationStrategy
{
    public function calculateOvertime(Attendance $attendance, Shift $shift): float;
}
