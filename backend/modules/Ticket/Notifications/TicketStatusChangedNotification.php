<?php

namespace Modules\Ticket\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Ticket\Entities\Ticket;

class TicketStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $oldStatus;
    protected $newStatus;
    protected $changedBy;
    protected $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, $oldStatus, $newStatus, $changedBy, $comment = null)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->changedBy = $changedBy;
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
        $oldStatusLabel = ucfirst(str_replace('_', ' ', $this->oldStatus));
        $newStatusLabel = ucfirst(str_replace('_', ' ', $this->newStatus));

        $mail = (new MailMessage)
            ->subject('Ticket Status Updated: ' . $this->ticket->ticket_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('The status of ticket ' . $this->ticket->ticket_number . ' has been updated.')
            ->line('**Ticket:** ' . $this->ticket->title)
            ->line('**Status changed from:** ' . $oldStatusLabel . ' **to:** ' . $newStatusLabel)
            ->line('**Changed by:** ' . $this->changedBy->name);

        if ($this->comment) {
            $mail->line('**Comment:** ' . $this->comment);
        }

        $mail->action('View Ticket', url('/landlord/tickets/' . $this->ticket->id))
             ->salutation('Best regards, ' . config('app.name'));

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => $this->ticket->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'changed_by' => $this->changedBy->name,
            'changed_by_id' => $this->changedBy->id,
            'comment' => $this->comment,
            'message' => 'Ticket status changed from ' . ucfirst(str_replace('_', ' ', $this->oldStatus)) . ' to ' . ucfirst(str_replace('_', ' ', $this->newStatus)),
            'action_url' => url('/landlord/tickets/' . $this->ticket->id),
            'type' => 'ticket_status_changed',
            'icon' => $this->getStatusIcon($this->newStatus),
            'color' => $this->getStatusColor($this->newStatus)
        ];
    }

    /**
     * Get icon for status
     */
    private function getStatusIcon($status): string
    {
        $icons = [
            'open' => 'fas fa-folder-open',
            'in_progress' => 'fas fa-spinner',
            'on_hold' => 'fas fa-pause-circle',
            'resolved' => 'fas fa-check-circle',
            'closed' => 'fas fa-times-circle'
        ];

        return $icons[$status] ?? 'fas fa-ticket-alt';
    }

    /**
     * Get color for status
     */
    private function getStatusColor($status): string
    {
        $colors = [
            'open' => 'primary',
            'in_progress' => 'warning',
            'on_hold' => 'secondary',
            'resolved' => 'success',
            'closed' => 'dark'
        ];

        return $colors[$status] ?? 'primary';
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType($notifiable): string
    {
        return 'ticket_status_changed';
    }
}
