<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\CRM\Domain\Events\LeadCreated;
use Modules\CRM\Domain\ValueObjects\ActivityType;
use Modules\CRM\Domain\ValueObjects\ActivityStatus;
use Modules\CRM\Infrastructure\Persistence\ActivityRepositoryInterface;

class CreateActivityOnLeadCreated implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private readonly ActivityRepositoryInterface $activities) {}

    public function handle(LeadCreated $event): void
    {
        $lead = $event->lead();
        $this->activities->create([
            'subject' => "New lead created: {$lead->name}",
            'description' => "Lead from {$lead->source} created in system",
            'type' => ActivityType::TASK->value,
            'status' => ActivityStatus::PLANNED->value,
            'related_type' => get_class($lead),
            'related_id' => $lead->id,
            'assigned_to' => $lead->assigned_to,
            'created_by' => $lead->created_by,
            'due_date' => now()->addDays(2),
        ]);
    }
}
