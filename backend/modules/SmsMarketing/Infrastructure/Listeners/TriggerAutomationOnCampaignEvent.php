<?php

namespace Modules\SmsMarketing\Infrastructure\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\SmsMarketing\Domain\Strategies\Automation\SmsAutomationActionInterface;
use Modules\SmsMarketing\Infrastructure\Persistence\SmAutomationRuleRepositoryInterface;

class TriggerAutomationOnCampaignEvent implements ShouldQueue
{
    public function __construct(
        private readonly SmAutomationRuleRepositoryInterface $ruleRepository,
        private readonly SmsAutomationActionInterface $automationAction,
    ) {}

    public function handle(object $event): void
    {
        $triggerType = class_basename($event);

        $rules = $this->ruleRepository->findActiveByTrigger($triggerType);

        foreach ($rules as $rule) {
            try {
                $this->automationAction->execute($rule, ['event' => $event]);
            } catch (\Throwable $e) {
                Log::error('SmsMarketing automation rule execution failed', [
                    'rule_id' => $rule->id,
                    'event' => $triggerType,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
