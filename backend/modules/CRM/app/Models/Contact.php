<?php

namespace Modules\CRM\Models;

use Modules\CRM\Domain\Entities\Contact as DomainContact;

/**
 * @deprecated Use Modules\CRM\Domain\Entities\Contact instead
 *
 * This class exists for backward compatibility.
 * All logic has been moved to the Domain\Entities\Contact rich domain entity.
 */
class Contact extends DomainContact
{
    // All implementation inherited from Domain\Entities\Contact
}
