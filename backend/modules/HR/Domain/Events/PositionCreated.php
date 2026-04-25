<?php

namespace Modules\HR\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Domain\Entities\Position;

class PositionCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Position $position) {}
}
