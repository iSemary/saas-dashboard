<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ProjectManagement\Domain\ValueObjects\ProjectStatus;

class ProjectStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $projectId,
        public ProjectStatus $from,
        public ProjectStatus $to
    ) {}
}
