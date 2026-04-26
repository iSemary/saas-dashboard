<?php

namespace Modules\SmsMarketing\Domain\Events;

use Modules\SmsMarketing\Domain\Entities\SmContact;

class SmContactOptedOut
{
    public function __construct(
        public readonly SmContact $contact,
        public readonly ?int $campaignId,
        public readonly ?string $reason,
    ) {}
}
