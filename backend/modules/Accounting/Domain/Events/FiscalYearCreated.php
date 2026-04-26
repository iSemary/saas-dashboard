<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Accounting\Domain\Entities\FiscalYear;

class FiscalYearCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly FiscalYear $fiscalYear,
    ) {}
}
