<?php

namespace Modules\HR\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Domain\Entities\Employee;

class EmployeePositionChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Employee $employee,
        public readonly ?int $oldPositionId,
        public readonly int $newPositionId,
        public readonly ?string $reason,
    ) {}
}
