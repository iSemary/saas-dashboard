<?php

namespace Modules\EmailMarketing\Domain\Strategies\Automation;

use Illuminate\Support\Facades\Log;
use Modules\EmailMarketing\Domain\Entities\EmAutomationRule;

class DefaultEmailAutomationAction implements EmailAutomationActionInterface
{
    public function execute(EmAutomationRule $rule, array $context = []): void
    {
        Log::info('EmailMarketing automation rule triggered', [
            'rule_id' => $rule->id,
            'rule_name' => $rule->name,
            'action_type' => $rule->action_type,
            'context' => $context,
        ]);
    }
}
