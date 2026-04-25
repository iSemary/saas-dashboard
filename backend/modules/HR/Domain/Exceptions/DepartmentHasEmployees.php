<?php

namespace Modules\HR\Domain\Exceptions;

class DepartmentHasEmployees extends HrDomainException
{
    public function __construct(int $departmentId, int $count)
    {
        parent::__construct("Cannot delete department {$departmentId} - it has {$count} employee(s). Reassign employees first.");
    }
}
