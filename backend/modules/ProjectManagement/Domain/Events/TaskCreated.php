<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $taskId,
        public string $projectId
    ) {}
}
