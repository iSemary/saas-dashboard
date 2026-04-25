<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\CRM\Domain\Entities\Contact;

class ContactCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Contact $contact, public readonly array $data = []) {}
}
