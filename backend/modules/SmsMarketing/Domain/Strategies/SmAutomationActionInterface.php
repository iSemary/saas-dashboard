<?php

namespace Modules\SmsMarketing\Domain\Strategies;

use Modules\SmsMarketing\Domain\Entities\SmAutomationRule;

interface SmAutomationActionInterface
{
    public function execute(SmAutomationRule $rule, array $context = []): void;
}
