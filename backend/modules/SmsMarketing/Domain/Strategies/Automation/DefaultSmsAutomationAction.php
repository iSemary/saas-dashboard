<?php

namespace Modules\SmsMarketing\Domain\Strategies\Automation;

use Illuminate\Support\Facades\Log;
use Modules\SmsMarketing\Domain\Entities\SmAutomationRule;

class DefaultSmsAutomationAction implements SmsAutomationActionInterface
{
    public function execute(SmAutomationRule $rule, array $context = []): void
    {
        Log::info('SmsMarketing automation rule triggered', [
            'rule_id' => $rule->id,
            'rule_name' => $rule->name,
            'action_type' => $rule->action_type,
            'context' => $context,
        ]);
    }
}
