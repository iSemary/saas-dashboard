<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Exceptions;

class InvalidFiscalYearTransition extends \RuntimeException
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Cannot transition fiscal year from '{$from}' to '{$to}'");
    }
}
