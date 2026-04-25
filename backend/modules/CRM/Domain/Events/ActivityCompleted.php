<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\CRM\Domain\Entities\Activity;

class ActivityCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Activity $activity,
        public readonly ?string $outcome = null,
        public readonly ?int $userId = null
    ) {}
}
