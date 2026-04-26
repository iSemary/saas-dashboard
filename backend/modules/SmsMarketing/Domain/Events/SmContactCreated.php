<?php

namespace Modules\SmsMarketing\Domain\Events;

use Modules\SmsMarketing\Domain\Entities\SmContact;

class SmContactCreated
{
    public function __construct(public readonly SmContact $contact) {}
}
