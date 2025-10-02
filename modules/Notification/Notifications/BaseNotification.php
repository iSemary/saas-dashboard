<?php

namespace Modules\Notification\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use App\Events\NotificationEvent;
use Modules\Notification\Repositories\NotificationChannelInterface;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $title;
    protected $body;
    protected $data;
    protected $type;
    protected $priority;
    protected $icon;
    protected $route;

    public function __construct($title, $body, $data = [], $type = 'info', $priority = 'low', $icon = null, $route = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->type = $type;
        $this->priority = $priority;
        $this->icon = $icon;
        $this->route = $route;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        $channels = ['database'];

        // Check if user has active push subscription
        $channelRepo = app(NotificationChannelInterface::class);
        $pushChannels = $channelRepo->getActiveChannels($notifiable->id, 'push');
        
        if ($pushChannels->isNotEmpty()) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->type,
            'priority' => $this->priority,
            'icon' => $this->icon,
            'route' => $this->route,
            'data' => $this->data,
            'is_read' => false,
        ];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        $channelRepo = app(NotificationChannelInterface::class);
        $subscription = $channelRepo->getActiveChannels($notifiable->id, 'push')->first();
        
        if (!$subscription) {
            return null;
        }

        return WebPushMessage::create()
            ->title($this->title)
            ->body($this->body)
            ->icon($this->icon ?: '/favicon.ico')
            ->badge('/favicon.ico')
            ->data([
                'id' => $notification->id,
                'route' => $this->route,
                'data' => $this->data,
            ])
            ->options([
                'TTL' => 3600, // Time to live in seconds
                'urgency' => $this->priority === 'high' ? 'high' : 'normal',
            ]);
    }

    /**
     * Handle the notification after it's been stored in database
     */
    public function afterDatabaseCommit($notifiable)
    {
        // Broadcast via WebSocket
        broadcast(new NotificationEvent(
            $notifiable->id,
            [
                'title' => $this->title,
                'message' => $this->body,
                'type' => $this->type,
                'priority' => $this->priority,
                'data' => $this->data,
                'route' => $this->route,
            ]
        ));
    }
}
