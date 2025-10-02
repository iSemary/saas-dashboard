<?php

namespace Modules\Comment\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Comment\Entities\Comment;

class CommentAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $objectType = class_basename($this->comment->object_model);
        $isReply = $this->comment->isReply();
        
        $subject = $isReply ? 'New Reply Added' : 'New Comment Added';
        $actionText = $isReply ? 'replied to' : 'commented on';

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->comment->user->name . ' ' . $actionText . ' a ' . strtolower($objectType) . '.');

        if ($isReply) {
            $mail->line('**Reply:** ' . $this->comment->excerpt);
        } else {
            $mail->line('**Comment:** ' . $this->comment->excerpt);
        }

        if ($this->comment->attachments->count() > 0) {
            $mail->line('**Attachments:** ' . $this->comment->attachments->count() . ' file(s) attached');
        }

        // Try to get the object title/name for context
        $objectTitle = $this->getObjectTitle();
        if ($objectTitle) {
            $mail->line('**' . $objectType . ':** ' . $objectTitle);
        }

        $mail->action('View ' . $objectType, $this->getObjectUrl())
             ->line('You can reply directly to continue the conversation.')
             ->salutation('Best regards, ' . config('app.name'));

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $objectType = class_basename($this->comment->object_model);
        $isReply = $this->comment->isReply();
        
        return [
            'comment_id' => $this->comment->id,
            'object_id' => $this->comment->object_id,
            'object_model' => $this->comment->object_model,
            'object_type' => $objectType,
            'user_name' => $this->comment->user->name,
            'user_id' => $this->comment->user_id,
            'comment_excerpt' => $this->comment->excerpt,
            'is_reply' => $isReply,
            'parent_id' => $this->comment->parent_id,
            'attachments_count' => $this->comment->attachments->count(),
            'message' => $this->comment->user->name . ' ' . ($isReply ? 'replied to' : 'commented on') . ' a ' . strtolower($objectType),
            'action_url' => $this->getObjectUrl(),
            'type' => $isReply ? 'comment_reply' : 'comment_added',
            'icon' => $isReply ? 'fas fa-reply' : 'fas fa-comment',
            'color' => 'info'
        ];
    }

    /**
     * Get the object title for context
     */
    private function getObjectTitle(): ?string
    {
        try {
            $model = app($this->comment->object_model);
            $object = $model->find($this->comment->object_id);
            
            if ($object) {
                // Try common title fields
                if (isset($object->title)) {
                    return $object->title;
                } elseif (isset($object->name)) {
                    return $object->name;
                } elseif (isset($object->subject)) {
                    return $object->subject;
                } elseif (method_exists($object, 'getTitle')) {
                    return $object->getTitle();
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail the notification
            \Log::warning('Could not get object title for comment notification', [
                'comment_id' => $this->comment->id,
                'object_model' => $this->comment->object_model,
                'object_id' => $this->comment->object_id,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Get the URL to view the object
     */
    private function getObjectUrl(): string
    {
        $objectType = class_basename($this->comment->object_model);
        
        // Generate URL based on object type
        switch ($objectType) {
            case 'Ticket':
                return url('/landlord/tickets/' . $this->comment->object_id);
            case 'Post':
                return url('/landlord/posts/' . $this->comment->object_id);
            default:
                // Generic fallback
                return url('/landlord/' . strtolower($objectType) . 's/' . $this->comment->object_id);
        }
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType($notifiable): string
    {
        return $this->comment->isReply() ? 'comment_reply' : 'comment_added';
    }
}
