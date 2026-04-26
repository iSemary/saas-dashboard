<?php

namespace Modules\EmailMarketing\Domain\Events;

use Modules\EmailMarketing\Domain\Entities\EmContact;

class EmContactUnsubscribed
{
    public function __construct(
        public readonly EmContact $contact,
        public readonly ?int $campaignId,
        public readonly ?string $reason,
    ) {}
}
