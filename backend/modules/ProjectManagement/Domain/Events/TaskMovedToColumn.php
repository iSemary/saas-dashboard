<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ProjectManagement\Domain\ValueObjects\TaskStatus;

class TaskMovedToColumn
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $taskId,
        public string $projectId,
        public ?string $fromColumnId,
        public string $toColumnId
    ) {}
}
