<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\CRM\Infrastructure\Persistence\CrmWebhookRepositoryInterface;

class DispatchWebhookOnDomainEvent implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private readonly CrmWebhookRepositoryInterface $webhooks) {}

    public function handle(object $event): void
    {
        $eventName = str_replace('Modules\\CRM\\Domain\\Events\\', '', get_class($event));
        $eventName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$1', $eventName));
        $hooks = $this->webhooks->getForEvent($eventName);
        foreach ($hooks as $hook) {
            $hook->dispatch($event);
        }
    }
}
