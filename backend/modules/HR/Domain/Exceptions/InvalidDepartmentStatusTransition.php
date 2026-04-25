<?php

namespace Modules\HR\Domain\Exceptions;

class InvalidDepartmentStatusTransition extends HrDomainException
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Cannot transition department status from '{$from}' to '{$to}'");
    }
}
