<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CalendarEventDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $eventId,
        public string $userId,
    ) {}
}
