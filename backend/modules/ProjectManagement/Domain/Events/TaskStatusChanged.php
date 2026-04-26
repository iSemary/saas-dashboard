<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ProjectManagement\Domain\ValueObjects\TaskStatus;

class TaskStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $taskId,
        public string $projectId,
        public TaskStatus $from,
        public TaskStatus $to
    ) {}
}
