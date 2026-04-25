<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\CRM\Domain\Entities\Lead;

class LeadCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Lead $lead, public readonly array $data = []) {}

    public function lead(): Lead { return $this->lead; }

    public function data(): array { return $this->data; }
}
