<?php

namespace Modules\HR\Domain\Strategies;

use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Domain\Entities\LeaveRequest;

interface LeaveApprovalStrategy
{
    public function getApprovers(LeaveRequest $leaveRequest): array;
    public function isAutoApproved(LeaveRequest $leaveRequest): bool;
    public function requiresMultiLevelApproval(LeaveRequest $leaveRequest): bool;
}
