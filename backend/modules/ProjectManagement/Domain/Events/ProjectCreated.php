<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ProjectManagement\Domain\Entities\Project;

class ProjectCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Project $project
    ) {}
}
