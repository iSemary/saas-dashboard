<?php

namespace Modules\CRM\Models;

use Modules\CRM\Domain\Entities\Lead as DomainLead;

/**
 * @deprecated Use Modules\CRM\Domain\Entities\Lead instead
 *
 * This class exists for backward compatibility.
 * All logic has been moved to the Domain\Entities\Lead rich domain entity.
 */
class Lead extends DomainLead
{
    // All implementation inherited from Domain\Entities\Lead
}
