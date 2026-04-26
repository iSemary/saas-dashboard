<?php

namespace Modules\SmsMarketing\Domain\Strategies\Automation;

use Modules\SmsMarketing\Domain\Entities\SmAutomationRule;

interface SmsAutomationActionInterface
{
    public function execute(SmAutomationRule $rule, array $context = []): void;
}
