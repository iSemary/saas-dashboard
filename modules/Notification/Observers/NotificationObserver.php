<?php

namespace Modules\Notification\Observers;

use App\Events\NotificationEvent;
use Modules\Notification\Entities\Notification;

class NotificationObserver
{
    /**
     * Handle the Notification "created" event.
     *
     * @param  \Modules\Notification\Entities\Notification  $notification
     * @return void
     */
    public function created(Notification $notification)
    {
        broadcast(new NotificationEvent(
            $notification->user_id,
            [
                "title" => $notification->title ?: $notification->name,
                "message" => $notification->body ?: $notification->description,
                "type" => $notification->type,
                "priority" => $notification->priority,
                "route" => $notification->route,
                "data" => $notification->data,
                "id" => $notification->id,
                "created_at" => $notification->created_at->toISOString(),
            ]
        ));
    }

    /**
     * Handle the Notification "updated" event.
     *
     * @param  \Modules\Notification\Entities\Notification  $notification
     * @return void
     */
    public function updated(Notification $notification)
    {
        //
    }

    /**
     * Handle the Notification "deleted" event.
     *
     * @param  \Modules\Notification\Entities\Notification  $notification
     * @return void
     */
    public function deleted(Notification $notification)
    {
        //
    }

    /**
     * Handle the Notification "restored" event.
     *
     * @param  \Modules\Notification\Entities\Notification  $notification
     * @return void
     */
    public function restored(Notification $notification)
    {
        //
    }

    /**
     * Handle the Notification "force deleted" event.
     *
     * @param  \Modules\Notification\Entities\Notification  $notification
     * @return void
     */
    public function forceDeleted(Notification $notification)
    {
        //
    }
}
