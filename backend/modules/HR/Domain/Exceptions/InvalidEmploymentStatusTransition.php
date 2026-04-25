<?php

namespace Modules\HR\Domain\Exceptions;

class InvalidEmploymentStatusTransition extends HrDomainException
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Cannot transition employment status from '{$from}' to '{$to}'");
    }
}
