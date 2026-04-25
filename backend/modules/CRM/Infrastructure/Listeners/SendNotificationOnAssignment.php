<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\CRM\Domain\Events\EntityAssigned;
use Modules\Auth\Entities\User;

class SendNotificationOnAssignment implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(EntityAssigned $event): void
    {
        $entity = $event->entity;
        $user = User::find($event->newUserId);
        if (!$user) return;
        
        $type = class_basename($entity);
        $title = "New {$type} Assigned";
        $message = "You have been assigned to {$type}: " . ($entity->name ?? $entity->subject ?? "#{$entity->id}");
        
        \Modules\Notification\Services\NotificationService::sendToUser($user, $title, $message, [
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
            'url' => "/crm/{$type}/{$entity->id}",
        ]);
    }
}
