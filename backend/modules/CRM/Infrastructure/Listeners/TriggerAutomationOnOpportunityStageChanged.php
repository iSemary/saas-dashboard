<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\CRM\Domain\Events\OpportunityStageChanged;
use Modules\CRM\Infrastructure\Persistence\CrmAutomationRuleRepositoryInterface;

class TriggerAutomationOnOpportunityStageChanged implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private readonly CrmAutomationRuleRepositoryInterface $automationRules) {}

    public function handle(OpportunityStageChanged $event): void
    {
        $rules = $this->automationRules->getForEvent('opportunity.stage_changed');
        foreach ($rules as $rule) {
            $rule->evaluateAndExecute($event->opportunity->toArray());
        }
    }
}
