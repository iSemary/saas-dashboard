<?php

namespace Modules\POS\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\POS\Domain\Entities\Damaged;

class DamagedRecorded
{
    use Dispatchable;

    public function __construct(public readonly Damaged $damaged) {}
}
