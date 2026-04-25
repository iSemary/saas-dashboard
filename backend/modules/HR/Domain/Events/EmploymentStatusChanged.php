<?php

namespace Modules\HR\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Domain\Entities\Employee;

class EmploymentStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Employee $employee,
        public readonly string $oldStatus,
        public readonly string $newStatus,
        public readonly ?string $reason,
    ) {}
}
