<?php

namespace Modules\Ticket\Services;

use Modules\Ticket\Entities\Ticket;
use Modules\Ticket\Repositories\TicketInterface;
use Modules\Comment\Services\CommentService;

class TicketService
{
    protected $repository;
    protected $commentService;
    public $model;

    public function __construct(TicketInterface $repository, Ticket $ticket, CommentService $commentService)
    {
        $this->model = $ticket;
        $this->repository = $repository;
        $this->commentService = $commentService;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getDataTables()
    {
        return $this->repository->datatables();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        // Set created_by if not provided
        if (!isset($data['created_by'])) {
            $data['created_by'] = auth()->id();
        }

        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function restore($id)
    {
        return $this->repository->restore($id);
    }

    /**
     * Get tickets by status for Kanban board
     */
    public function getTicketsByStatus($status)
    {
        return $this->repository->getTicketsByStatus($status);
    }

    /**
     * Get Kanban board data
     */
    public function getKanbanData()
    {
        return $this->repository->getKanbanData();
    }

    /**
     * Update ticket status
     */
    public function updateStatus($id, $newStatus, $comment = null)
    {
        return $this->repository->updateStatus($id, $newStatus, auth()->id(), $comment);
    }

    /**
     * Get tickets for a specific user
     */
    public function getTicketsByUser($userId)
    {
        return $this->repository->getTicketsByUser($userId);
    }

    /**
     * Get my tickets (created by or assigned to current user)
     */
    public function getMyTickets()
    {
        return $this->repository->getTicketsByUser(auth()->id());
    }

    /**
     * Get overdue tickets
     */
    public function getOverdueTickets()
    {
        return $this->repository->getOverdueTickets();
    }

    /**
     * Get ticket statistics
     */
    public function getTicketStats()
    {
        return $this->repository->getTicketStats();
    }

    /**
     * Search tickets
     */
    public function searchTickets($query)
    {
        return $this->repository->searchTickets($query);
    }

    /**
     * Get ticket with comments
     */
    public function getTicketWithComments($id)
    {
        $ticket = $this->repository->find($id);
        if (!$ticket) {
            return null;
        }

        // Get comments using the comment service
        $comments = $this->commentService->getCommentsForObject($id, Ticket::class);
        
        return [
            'ticket' => $ticket,
            'comments' => $comments,
            'comment_stats' => $this->commentService->getCommentStats($id, Ticket::class)
        ];
    }

    /**
     * Add comment to ticket
     */
    public function addComment($ticketId, array $commentData)
    {
        $commentData['object_id'] = $ticketId;
        $commentData['object_model'] = Ticket::class;
        $commentData['user_id'] = auth()->id();

        return $this->commentService->create($commentData);
    }

    /**
     * Assign ticket to user
     */
    public function assignTicket($ticketId, $userId, $comment = null)
    {
        $ticket = $this->repository->find($ticketId);
        if (!$ticket) {
            return null;
        }

        $oldAssignee = $ticket->assigned_to;
        $ticket = $this->repository->update($ticketId, ['assigned_to' => $userId]);

        // Add comment about assignment
        if ($comment || $oldAssignee !== $userId) {
            $assigneeName = \App\Models\User::find($userId)->name ?? 'Unknown';
            $assignmentComment = $comment ?: "Ticket assigned to {$assigneeName}";
            
            $this->addComment($ticketId, [
                'comment' => $assignmentComment,
                'user_id' => auth()->id()
            ]);
        }

        return $ticket;
    }

    /**
     * Close ticket
     */
    public function closeTicket($ticketId, $comment = null)
    {
        $ticket = $this->repository->updateStatus($ticketId, 'closed', auth()->id(), $comment);
        
        if ($comment) {
            $this->addComment($ticketId, [
                'comment' => $comment,
                'user_id' => auth()->id()
            ]);
        }

        return $ticket;
    }

    /**
     * Reopen ticket
     */
    public function reopenTicket($ticketId, $comment = null)
    {
        $ticket = $this->repository->updateStatus($ticketId, 'open', auth()->id(), $comment);
        
        if ($comment) {
            $this->addComment($ticketId, [
                'comment' => $comment,
                'user_id' => auth()->id()
            ]);
        }

        return $ticket;
    }

    /**
     * Get filtered tickets
     */
    public function getFilteredTickets(array $filters = [])
    {
        return $this->repository->getFilteredTickets($filters);
    }

    /**
     * Get SLA performance data
     */
    public function getSlaPerformance()
    {
        return $this->repository->getSlaPerformance();
    }

    /**
     * Get dashboard data
     */
    public function getDashboardData()
    {
        return [
            'stats' => $this->getTicketStats(),
            'overdue_tickets' => $this->getOverdueTickets(),
            'my_tickets' => $this->getMyTickets(),
            'recent_activity' => $this->getRecentActivity(),
            'sla_performance' => $this->getSlaPerformance(),
        ];
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity($limit = 10)
    {
        // This could combine ticket status changes and comments
        $stats = $this->getTicketStats();
        return $stats['recent_activity'] ?? [];
    }

    /**
     * Bulk update tickets
     */
    public function bulkUpdateTickets(array $ticketIds, array $data)
    {
        $results = [];
        
        foreach ($ticketIds as $ticketId) {
            $result = $this->repository->update($ticketId, $data);
            if ($result) {
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     * Bulk assign tickets
     */
    public function bulkAssignTickets(array $ticketIds, $userId, $comment = null)
    {
        $results = [];
        
        foreach ($ticketIds as $ticketId) {
            $result = $this->assignTicket($ticketId, $userId, $comment);
            if ($result) {
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     * Get ticket timeline (status changes + comments)
     */
    public function getTicketTimeline($ticketId)
    {
        $ticket = $this->repository->find($ticketId);
        if (!$ticket) {
            return null;
        }

        $timeline = [];

        // Add status logs
        foreach ($ticket->statusLogs as $log) {
            $timeline[] = [
                'type' => 'status_change',
                'id' => $log->id,
                'title' => $log->status_change_description,
                'description' => $log->comment,
                'user' => $log->changedBy->name ?? 'System',
                'user_avatar' => $log->changedBy->avatar ?? null,
                'timestamp' => $log->created_at,
                'data' => [
                    'old_status' => $log->old_status,
                    'new_status' => $log->new_status,
                    'time_in_previous' => $log->formatted_time_in_previous_status,
                ]
            ];
        }

        // Add comments
        $comments = $this->commentService->getCommentsForObject($ticketId, Ticket::class);
        foreach ($comments as $comment) {
            $timeline[] = [
                'type' => 'comment',
                'id' => $comment->id,
                'title' => 'Comment added',
                'description' => $comment->comment,
                'user' => $comment->user->name ?? 'Unknown',
                'user_avatar' => $comment->user->avatar ?? null,
                'timestamp' => $comment->created_at,
                'data' => [
                    'attachments_count' => $comment->attachments->count(),
                    'reactions_count' => $comment->reactions->count(),
                    'replies_count' => $comment->replies->count(),
                ]
            ];
        }

        // Sort by timestamp
        usort($timeline, function ($a, $b) {
            return $a['timestamp']->timestamp <=> $b['timestamp']->timestamp;
        });

        return $timeline;
    }

    /**
     * Get ticket metrics for reporting
     */
    public function getTicketMetrics($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: now()->subMonth();
        $endDate = $endDate ?: now();

        return [
            'created_count' => $this->model->whereBetween('created_at', [$startDate, $endDate])->count(),
            'resolved_count' => $this->model->whereBetween('resolved_at', [$startDate, $endDate])->count(),
            'closed_count' => $this->model->where('status', 'closed')->whereBetween('closed_at', [$startDate, $endDate])->count(),
            'average_resolution_time' => $this->calculateAverageResolutionTime($startDate, $endDate),
            'tickets_by_priority' => $this->getTicketsByPriorityInPeriod($startDate, $endDate),
            'tickets_by_status' => $this->getTicketsByStatusInPeriod($startDate, $endDate),
        ];
    }

    protected function calculateAverageResolutionTime($startDate, $endDate)
    {
        $resolvedTickets = $this->model->whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$startDate, $endDate])
            ->get();

        if ($resolvedTickets->isEmpty()) {
            return 0;
        }

        $totalTime = $resolvedTickets->sum(function ($ticket) {
            return $ticket->created_at->diffInSeconds($ticket->resolved_at);
        });

        return $totalTime / $resolvedTickets->count();
    }

    protected function getTicketsByPriorityInPeriod($startDate, $endDate)
    {
        $priorities = [];
        foreach (Ticket::getPriorities() as $priority => $label) {
            $priorities[$priority] = $this->model->where('priority', $priority)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
        }
        return $priorities;
    }

    protected function getTicketsByStatusInPeriod($startDate, $endDate)
    {
        $statuses = [];
        foreach (Ticket::getStatuses() as $status => $label) {
            $statuses[$status] = $this->model->where('status', $status)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
        }
        return $statuses;
    }
}
