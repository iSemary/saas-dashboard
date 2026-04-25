<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\CRM\Domain\Entities\Opportunity;

class OpportunityClosedLost
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Opportunity $opportunity,
        public readonly ?string $reason = null,
        public readonly ?int $userId = null
    ) {}
}
