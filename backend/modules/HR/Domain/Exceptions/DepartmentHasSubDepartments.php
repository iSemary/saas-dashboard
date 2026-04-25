<?php

namespace Modules\HR\Domain\Exceptions;

class DepartmentHasSubDepartments extends HrDomainException
{
    public function __construct(int $departmentId, int $count)
    {
        parent::__construct("Cannot delete department {$departmentId} - it has {$count} sub-department(s). Reassign sub-departments first.");
    }
}
