<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimerStarted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $sessionId,
        public string $userId,
        public ?string $projectId = null,
        public ?string $taskId = null,
    ) {}
}
