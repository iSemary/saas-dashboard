<?php

namespace Modules\CRM\Models;

use Modules\CRM\Domain\Entities\Company as DomainCompany;

/**
 * @deprecated Use Modules\CRM\Domain\Entities\Company instead
 *
 * This class exists for backward compatibility.
 * All logic has been moved to the Domain\Entities\Company rich domain entity.
 */
class Company extends DomainCompany
{
    // All implementation inherited from Domain\Entities\Company
}
