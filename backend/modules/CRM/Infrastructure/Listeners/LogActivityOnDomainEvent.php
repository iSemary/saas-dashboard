<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogActivityOnDomainEvent implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(object $event): void
    {
        \Log::info('CRM Domain Event', [
            'event' => get_class($event),
            'timestamp' => now(),
        ]);
    }
}
