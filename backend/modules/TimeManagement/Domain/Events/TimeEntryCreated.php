<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimeEntryCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $entryId,
        public string $userId,
        public int $durationMinutes,
    ) {}
}
