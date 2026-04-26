<?php

namespace Modules\Ticket\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Ticket\Entities\Ticket;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
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
        return (new MailMessage)
            ->subject('New Ticket Created: ' . $this->ticket->ticket_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new ticket has been created and requires your attention.')
            ->line('**Ticket Number:** ' . $this->ticket->ticket_number)
            ->line('**Title:** ' . $this->ticket->title)
            ->line('**Priority:** ' . ucfirst($this->ticket->priority))
            ->line('**Status:** ' . ucfirst(str_replace('_', ' ', $this->ticket->status)))
            ->line('**Created by:** ' . $this->ticket->creator->name)
            ->action('View Ticket', url('/landlord/tickets/' . $this->ticket->id))
            ->line('Please review and take appropriate action.')
            ->salutation('Best regards, ' . config('app.name'));
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
            'priority' => $this->ticket->priority,
            'status' => $this->ticket->status,
            'created_by' => $this->ticket->creator->name,
            'created_by_id' => $this->ticket->created_by,
            'message' => translate('message.action_completed'),
            'action_url' => url('/landlord/tickets/' . $this->ticket->id),
            'type' => 'ticket_created',
            'icon' => 'fas fa-ticket-alt',
            'color' => 'primary'
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType($notifiable): string
    {
        return 'ticket_created';
    }
}
