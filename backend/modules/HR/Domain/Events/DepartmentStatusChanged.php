<?php

namespace Modules\HR\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Domain\Entities\Department;

class DepartmentStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Department $department,
        public readonly string $oldStatus,
        public readonly string $newStatus,
    ) {}
}
