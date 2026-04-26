<?php

namespace Modules\EmailMarketing\Infrastructure\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\EmailMarketing\Domain\Strategies\Automation\EmailAutomationActionInterface;
use Modules\EmailMarketing\Infrastructure\Persistence\EmAutomationRuleRepositoryInterface;

class TriggerAutomationOnCampaignEvent implements ShouldQueue
{
    public function __construct(
        private readonly EmAutomationRuleRepositoryInterface $ruleRepository,
        private readonly EmailAutomationActionInterface $automationAction,
    ) {}

    public function handle(object $event): void
    {
        $triggerType = class_basename($event);

        $rules = $this->ruleRepository->findActiveByTrigger($triggerType);

        foreach ($rules as $rule) {
            try {
                $this->automationAction->execute($rule, ['event' => $event]);
            } catch (\Throwable $e) {
                Log::error('EmailMarketing automation rule execution failed', [
                    'rule_id' => $rule->id,
                    'event' => $triggerType,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
