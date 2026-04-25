<?php

namespace Modules\HR\Domain\Exceptions;

class CircularDepartmentHierarchy extends HrDomainException
{
    public function __construct(int $departmentId, int $parentId)
    {
        parent::__construct("Cannot set department {$parentId} as parent of {$departmentId} - would create circular hierarchy");
    }
}
