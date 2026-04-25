<?php

namespace Modules\CRM\Models;

use Modules\CRM\Domain\Entities\Opportunity as DomainOpportunity;

/**
 * @deprecated Use Modules\CRM\Domain\Entities\Opportunity instead
 *
 * This class exists for backward compatibility.
 * All logic has been moved to the Domain\Entities\Opportunity rich domain entity.
 */
class Opportunity extends DomainOpportunity
{
    // All implementation inherited from Domain\Entities\Opportunity
}
