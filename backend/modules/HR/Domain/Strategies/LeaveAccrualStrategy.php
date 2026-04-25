<?php

namespace Modules\HR\Domain\Strategies;

use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Domain\Entities\LeavePolicy;

interface LeaveAccrualStrategy
{
    public function calculateAccrual(Employee $employee, LeavePolicy $policy, int $year): float;
    public function getAccrualPeriod(): string;
}
