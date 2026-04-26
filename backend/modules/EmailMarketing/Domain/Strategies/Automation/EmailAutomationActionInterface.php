<?php

namespace Modules\EmailMarketing\Domain\Strategies\Automation;

use Modules\EmailMarketing\Domain\Entities\EmAutomationRule;

interface EmailAutomationActionInterface
{
    public function execute(EmAutomationRule $rule, array $context = []): void;
}
