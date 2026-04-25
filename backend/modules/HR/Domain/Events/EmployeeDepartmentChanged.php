<?php

namespace Modules\HR\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Domain\Entities\Employee;

class EmployeeDepartmentChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Employee $employee,
        public readonly ?int $oldDepartmentId,
        public readonly int $newDepartmentId,
        public readonly ?string $reason,
    ) {}
}
