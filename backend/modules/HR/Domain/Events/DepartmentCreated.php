<?php

namespace Modules\HR\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Domain\Entities\Department;

class DepartmentCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Department $department) {}
}
