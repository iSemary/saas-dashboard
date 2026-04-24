<?php

namespace Modules\Notification\Examples;

use Modules\Notification\Notifications\GenericNotification;
use Modules\Auth\Entities\User;

/**
 * Example usage of the enhanced notification system
 * 
 * This file demonstrates how to use the new notification features
 * from anywhere in your application (Tickets, Comments, etc.)
 */
class NotificationUsageExample
{
    /**
     * Example: Send a ticket notification
     */
    public function sendTicketNotification($userId, $ticketId, $ticketTitle)
    {
        $user = User::find($userId);
        
        if ($user) {
            $notification = GenericNotification::ticket(
                'New Ticket Created',
                "A new ticket '{$ticketTitle}' has been created and assigned to you.",
                $ticketId,
                route('tickets.show', $ticketId)
            );
            
            $user->notify($notification);
        }
    }

    /**
     * Example: Send a comment notification
     */
    public function sendCommentNotification($userId, $commentId, $commentContent)
    {
        $user = User::find($userId);
        
        if ($user) {
            $notification = GenericNotification::comment(
                'New Comment',
                "Someone commented: " . substr($commentContent, 0, 100) . "...",
                $commentId,
                route('comments.show', $commentId)
            );
            
            $user->notify($notification);
        }
    }

    /**
     * Example: Send an alert notification
     */
    public function sendAlertNotification($userId, $alertMessage, $data = [])
    {
        $user = User::find($userId);
        
        if ($user) {
            $notification = GenericNotification::alert(
                'System Alert',
                $alertMessage,
                $data
            );
            
            $user->notify($notification);
        }
    }

    /**
     * Example: Send an announcement
     */
    public function sendAnnouncementNotification($userId, $title, $message, $data = [])
    {
        $user = User::find($userId);
        
        if ($user) {
            $notification = GenericNotification::announcement(
                $title,
                $message,
                $data
            );
            
            $user->notify($notification);
        }
    }

    /**
     * Example: Send a custom notification with attachments
     */
    public function sendNotificationWithAttachments($userId, $title, $body, $attachments = [])
    {
        $user = User::find($userId);
        
        if ($user) {
            $notification = new GenericNotification(
                $title,
                $body,
                [
                    'attachments' => $attachments,
                    'custom_data' => 'any additional data'
                ],
                'info',
                'medium',
                '/assets/shared/images/icons/attachment.png'
            );
            
            $user->notify($notification);
        }
    }

    /**
     * Example: Bulk notification to multiple users
     */
    public function sendBulkNotification($userIds, $title, $body, $type = 'info')
    {
        $users = User::whereIn('id', $userIds)->get();
        
        foreach ($users as $user) {
            $notification = new GenericNotification(
                $title,
                $body,
                ['bulk' => true],
                $type
            );
            
            $user->notify($notification);
        }
    }
}
