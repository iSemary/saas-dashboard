<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Exceptions;

class FiscalYearClosed extends \RuntimeException
{
    public function __construct(string $fiscalYearName)
    {
        parent::__construct("Fiscal year '{$fiscalYearName}' is closed and cannot accept new entries");
    }
}
