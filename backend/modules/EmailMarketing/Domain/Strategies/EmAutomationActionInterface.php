<?php

namespace Modules\EmailMarketing\Domain\Strategies;

use Modules\EmailMarketing\Domain\Entities\EmAutomationRule;

interface EmAutomationActionInterface
{
    public function execute(EmAutomationRule $rule, array $context = []): void;
}
