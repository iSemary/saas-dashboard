<?php

namespace Modules\EmailMarketing\Domain\Events;

use Modules\EmailMarketing\Domain\Entities\EmContact;

class EmContactCreated
{
    public function __construct(public readonly EmContact $contact) {}
}
