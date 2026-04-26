<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MilestoneCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $milestoneId,
        public string $projectId
    ) {}
}
