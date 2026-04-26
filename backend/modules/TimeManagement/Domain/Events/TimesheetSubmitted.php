<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\TimeManagement\Domain\ValueObjects\TimesheetStatus;

class TimesheetSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $timesheetId,
        public string $userId,
    ) {}
}
