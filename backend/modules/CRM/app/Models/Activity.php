<?php

namespace Modules\CRM\Models;

use Modules\CRM\Domain\Entities\Activity as DomainActivity;

/**
 * @deprecated Use Modules\CRM\Domain\Entities\Activity instead
 *
 * This class exists for backward compatibility.
 * All logic has been moved to the Domain\Entities\Activity rich domain entity.
 */
class Activity extends DomainActivity
{
    // All implementation inherited from Domain\Entities\Activity
}
