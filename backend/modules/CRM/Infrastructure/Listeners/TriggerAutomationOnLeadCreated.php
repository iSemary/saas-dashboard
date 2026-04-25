<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\CRM\Domain\Events\LeadCreated;
use Modules\CRM\Infrastructure\Persistence\CrmAutomationRuleRepositoryInterface;

class TriggerAutomationOnLeadCreated implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private readonly CrmAutomationRuleRepositoryInterface $automationRules) {}

    public function handle(LeadCreated $event): void
    {
        $rules = $this->automationRules->getForEvent('lead.created');
        foreach ($rules as $rule) {
            $rule->evaluateAndExecute($event->lead()->toArray());
        }
    }
}
