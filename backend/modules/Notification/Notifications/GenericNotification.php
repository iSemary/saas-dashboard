<?php

namespace Modules\Notification\Notifications;

/**
 * Generic notification class that can be used for any type of notification
 */
class GenericNotification extends BaseNotification
{
    public function __construct($title, $body, $data = [], $type = 'info', $priority = 'low', $icon = null, $route = null)
    {
        parent::__construct($title, $body, $data, $type, $priority, $icon, $route);
    }

    /**
     * Create a ticket notification
     */
    public static function ticket($title, $body, $ticketId, $route = null)
    {
        return new static(
            $title,
            $body,
            ['type' => 'ticket', 'ticket_id' => $ticketId],
            'info',
            'medium',
            '/assets/shared/images/icons/ticket.png',
            $route
        );
    }

    /**
     * Create a comment notification
     */
    public static function comment($title, $body, $commentId, $route = null)
    {
        return new static(
            $title,
            $body,
            ['type' => 'comment', 'comment_id' => $commentId],
            'info',
            'low',
            '/assets/shared/images/icons/comment.png',
            $route
        );
    }

    /**
     * Create an alert notification
     */
    public static function alert($title, $body, $data = [], $route = null)
    {
        return new static(
            $title,
            $body,
            $data,
            'alert',
            'high',
            '/assets/shared/images/icons/alert.png',
            $route
        );
    }

    /**
     * Create an announcement notification
     */
    public static function announcement($title, $body, $data = [], $route = null)
    {
        return new static(
            $title,
            $body,
            $data,
            'announcement',
            'medium',
            '/assets/shared/images/icons/announcement.png',
            $route
        );
    }
}
