<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TicketRepository extends BaseRepository implements TicketRepositoryInterface
{
    public function __construct(Ticket $ticket)
    {
        parent::__construct($ticket);
    }

    /**
     * Get ticket detail with all relations
     */
    public function getDetailWithRelations(int $id): ?Model
    {
        return $this->model->newQuery()
            ->with([
                'creator',
                'assignee',
                'brand',
                'comments.user',
                'comments.attachments',
            ])
            ->find($id);
    }

    /**
     * Add comment to ticket
     */
    public function addComment(int $ticketId, array $data): ?Model
    {
        $ticket = $this->find($ticketId);
        if (!$ticket) {
            return null;
        }

        $comment = $ticket->comments()->create([
            'comment' => $data['comment'],
            'is_private' => $data['is_private'] ?? false,
            'user_id' => $data['user_id'],
        ]);

        // Handle attachments if present
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                $path = $file->store('ticket-attachments/' . $ticket->id);
                $comment->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        return $comment->load('user', 'attachments');
    }

    /**
     * Assign ticket to user
     */
    public function assignToUser(int $ticketId, int $userId): ?Model
    {
        $ticket = $this->find($ticketId);
        if (!$ticket) {
            return null;
        }

        $oldAssignee = $ticket->assigned_to;
        
        $ticket->assigned_to = $userId;
        $ticket->save();

        // Log activity
        $this->logActivity($ticket, 'assignment_changed', [
            'old_assignee' => $oldAssignee,
            'new_assignee' => $userId,
        ]);

        return $ticket->load('assignee');
    }

    /**
     * Change ticket status
     */
    public function changeStatus(int $ticketId, string $status, ?string $comment = null): ?Model
    {
        $ticket = $this->find($ticketId);
        if (!$ticket) {
            return null;
        }

        $oldStatus = $ticket->status;
        
        $ticket->status = $status;
        
        // Set resolved/closed timestamps
        if ($status === 'resolved' && !$ticket->resolved_at) {
            $ticket->resolved_at = now();
        } elseif ($status === 'closed' && !$ticket->closed_at) {
            $ticket->closed_at = now();
        }
        
        $ticket->save();

        // Log activity
        $this->logActivity($ticket, 'status_changed', [
            'old_status' => $oldStatus,
            'new_status' => $status,
            'comment' => $comment,
        ]);

        return $ticket;
    }

    /**
     * Change ticket priority
     */
    public function changePriority(int $ticketId, string $priority): ?Model
    {
        $ticket = $this->find($ticketId);
        if (!$ticket) {
            return null;
        }

        $oldPriority = $ticket->priority;
        
        $ticket->priority = $priority;
        $ticket->save();

        // Log activity
        $this->logActivity($ticket, 'priority_changed', [
            'old_priority' => $oldPriority,
            'new_priority' => $priority,
        ]);

        return $ticket;
    }

    /**
     * Close ticket
     */
    public function close(int $ticketId): ?Model
    {
        $ticket = $this->find($ticketId);
        if (!$ticket) {
            return null;
        }

        $ticket->status = 'closed';
        $ticket->closed_at = now();
        $ticket->save();

        $this->logActivity($ticket, 'closed');

        return $ticket;
    }

    /**
     * Reopen ticket
     */
    public function reopen(int $ticketId): ?Model
    {
        $ticket = $this->find($ticketId);
        if (!$ticket) {
            return null;
        }

        $ticket->status = 'open';
        $ticket->closed_at = null;
        $ticket->save();

        $this->logActivity($ticket, 'reopened');

        return $ticket;
    }

    /**
     * Get activity timeline for ticket
     */
    public function getActivityTimeline(Model $ticket): array
    {
        $timeline = [];
        
        // Add creation
        $timeline[] = [
            'type' => 'created',
            'title' => 'Ticket Created',
            'description' => null,
            'user' => $ticket->creator?->name ?? 'System',
            'user_avatar' => $ticket->creator?->avatar,
            'timestamp' => $ticket->created_at,
        ];

        // Add activities from log
        $activities = $ticket->activities()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($activities as $activity) {
            $timeline[] = [
                'type' => $activity->type,
                'title' => $this->getActivityTitle($activity->type),
                'description' => $this->getActivityDescription($activity),
                'user' => $activity->user?->name ?? 'System',
                'user_avatar' => $activity->user?->avatar,
                'timestamp' => $activity->created_at,
            ];
        }

        return $timeline;
    }

    /**
     * Log activity
     */
    public function logActivity(Model $ticket, string $type, array $data = []): void
    {
        $ticket->activities()->create([
            'type' => $type,
            'data' => $data,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Get SLA metrics for ticket
     */
    public function getSLAMetrics(Model $ticket): array
    {
        return [
            'time_in_current_status' => $this->calculateTimeInCurrentStatus($ticket),
            'status_history' => $ticket->statusHistory ?? [],
            'is_overdue' => $this->isOverdue($ticket),
        ];
    }

    /**
     * Get tickets by status for kanban view
     */
    public function getByStatus(string $status, array $filters = []): array
    {
        $query = $this->model->newQuery()
            ->where('status', $status)
            ->with(['assignee', 'creator', 'brand']);

        // Apply additional filters
        if (!empty($filters['assignee_id'])) {
            $query->where('assigned_to', $filters['assignee_id']);
        }
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    /**
     * Get activity title
     */
    protected function getActivityTitle(string $type): string
    {
        $titles = [
            'status_changed' => 'Status Changed',
            'priority_changed' => 'Priority Changed',
            'assignment_changed' => 'Assignment Changed',
            'comment_added' => 'Comment Added',
            'closed' => 'Ticket Closed',
            'reopened' => 'Ticket Reopened',
        ];

        return $titles[$type] ?? 'Activity';
    }

    /**
     * Get activity description
     */
    protected function getActivityDescription($activity): ?string
    {
        $data = $activity->data ?? [];
        
        switch ($activity->type) {
            case 'status_changed':
                return "From {$data['old_status']} to {$data['new_status']}";
            case 'priority_changed':
                return "From {$data['old_priority']} to {$data['new_priority']}";
            case 'assignment_changed':
                $oldUser = User::find($data['old_assignee'])?->name ?? 'Unassigned';
                $newUser = User::find($data['new_assignee'])?->name ?? 'Unassigned';
                return "From {$oldUser} to {$newUser}";
            default:
                return null;
        }
    }

    /**
     * Calculate time in current status
     */
    protected function calculateTimeInCurrentStatus(Model $ticket): int
    {
        // Get last status change activity
        $lastActivity = $ticket->activities()
            ->where('type', 'status_changed')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastActivity) {
            return now()->diffInSeconds($lastActivity->created_at);
        }

        // If no status change, calculate from creation
        return now()->diffInSeconds($ticket->created_at);
    }

    /**
     * Check if ticket is overdue
     */
    protected function isOverdue(Model $ticket): bool
    {
        if (!$ticket->due_date) {
            return false;
        }

        return now()->greaterThan($ticket->due_date);
    }
}
