<?php

namespace Modules\Ticket\Repositories;

use App\Helpers\TableHelper;
use Illuminate\Support\Facades\DB;
use Modules\Ticket\Entities\Ticket;
use Modules\Ticket\Entities\TicketStatusLog;
use Yajra\DataTables\DataTables;

class TicketRepository implements TicketInterface
{
    protected $model;

    public function __construct(Ticket $ticket)
    {
        $this->model = $ticket;
    }

    public function all()
    {
        return $this->model->with(['creator', 'assignee', 'brand'])->get();
    }

    public function datatables()
    {
        $rows = $this->model->query()
            ->with(['creator', 'assignee', 'brand'])
            ->withCount(['comments', 'statusLogs'])
            ->where(function ($q) {
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                }
                
                // Filter by status if provided
                if (request()->status) {
                    $q->where('status', request()->status);
                }
                
                // Filter by priority if provided
                if (request()->priority) {
                    $q->where('priority', request()->priority);
                }
                
                // Filter by assigned user if provided
                if (request()->assigned_to) {
                    $q->where('assigned_to', request()->assigned_to);
                }
            });

        return DataTables::of($rows)
            ->addColumn('creator_name', function ($row) {
                return $row->creator->name ?? 'Unknown';
            })
            ->addColumn('assignee_name', function ($row) {
                return $row->assignee->name ?? 'Unassigned';
            })
            ->addColumn('brand_name', function ($row) {
                return $row->brand->name ?? 'N/A';
            })
            ->addColumn('status_badge', function ($row) {
                $class = $row->getStatusBadgeClass();
                $status = ucfirst(str_replace('_', ' ', $row->status));
                return '<span class="badge ' . $class . '">' . $status . '</span>';
            })
            ->addColumn('priority_badge', function ($row) {
                $class = $row->getPriorityBadgeClass();
                $priority = ucfirst($row->priority);
                return '<span class="badge ' . $class . '">' . $priority . '</span>';
            })
            ->addColumn('overdue_indicator', function ($row) {
                if ($row->isOverdue()) {
                    return '<i class="fas fa-exclamation-triangle text-danger" title="Overdue"></i>';
                }
                return '';
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.tickets.edit',
                    deleteRoute: 'landlord.tickets.destroy',
                    restoreRoute: 'landlord.tickets.restore',
                    type: "tickets",
                    titleType: "ticket",
                    showIconsOnly: true
                );
            })
            ->rawColumns(['actions', 'status_badge', 'priority_badge', 'overdue_indicator'])
            ->make(true);
    }

    public function find($id)
    {
        return $this->model->with([
            'creator', 
            'assignee', 
            'brand', 
            'statusLogs.changedBy', 
            'comments.user',
            'comments.attachments',
            'comments.reactions'
        ])->find($id);
    }

    public function create(array $data)
    {
        $ticket = $this->model->create($data);
        
        // Create initial status log
        TicketStatusLog::create([
            'ticket_id' => $ticket->id,
            'old_status' => null,
            'new_status' => $ticket->status,
            'changed_by' => $ticket->created_by,
            'comment' => 'Ticket created'
        ]);
        
        return $ticket;
    }

    public function update($id, array $data)
    {
        $ticket = $this->model->find($id);
        if ($ticket) {
            $oldStatus = $ticket->status;
            $ticket->update($data);
            
            // Log status change if status was updated
            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                TicketStatusLog::create([
                    'ticket_id' => $ticket->id,
                    'old_status' => $oldStatus,
                    'new_status' => $data['status'],
                    'changed_by' => auth()->id(),
                    'comment' => $data['status_comment'] ?? null
                ]);
            }
            
            return $ticket;
        }
        return null;
    }

    public function delete($id)
    {
        $ticket = $this->model->find($id);
        if ($ticket) {
            $ticket->delete();
            return true;
        }
        return false;
    }

    public function restore($id)
    {
        $ticket = $this->model->withTrashed()->find($id);
        if ($ticket) {
            $ticket->restore();
            return true;
        }
        return false;
    }

    public function getTicketsByStatus($status)
    {
        return $this->model->byStatus($status)
            ->with(['creator', 'assignee', 'brand'])
            ->withCount('comments')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getKanbanData()
    {
        $statuses = Ticket::getStatuses();
        $kanbanData = [];

        foreach ($statuses as $statusKey => $statusLabel) {
            $tickets = $this->getTicketsByStatus($statusKey);
            
            $kanbanData[$statusKey] = [
                'label' => $statusLabel,
                'count' => $tickets->count(),
                'tickets' => $tickets->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'ticket_number' => $ticket->ticket_number,
                        'title' => $ticket->title,
                        'priority' => $ticket->priority,
                        'priority_badge_class' => $ticket->getPriorityBadgeClass(),
                        'creator' => $ticket->creator->name ?? 'Unknown',
                        'assignee' => $ticket->assignee->name ?? 'Unassigned',
                        'assignee_avatar' => $ticket->assignee->avatar ?? null,
                        'created_at' => $ticket->created_at->format('M d, Y'),
                        'due_date' => $ticket->due_date ? $ticket->due_date->format('M d, Y') : null,
                        'is_overdue' => $ticket->isOverdue(),
                        'comments_count' => $ticket->comments_count,
                        'tags' => $ticket->tags ?? [],
                        'time_in_status' => $ticket->formatDuration($ticket->getTimeInCurrentStatus()),
                    ];
                })
            ];
        }

        return $kanbanData;
    }

    public function updateStatus($id, $newStatus, $userId, $comment = null)
    {
        $ticket = $this->model->find($id);
        if (!$ticket) {
            return null;
        }

        $oldStatus = $ticket->status;
        $ticket->update(['status' => $newStatus]);

        // Create status log
        TicketStatusLog::create([
            'ticket_id' => $ticket->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $userId,
            'comment' => $comment
        ]);

        return $ticket->fresh(['creator', 'assignee', 'brand']);
    }

    public function getTicketsByUser($userId)
    {
        return $this->model->where(function ($query) use ($userId) {
            $query->where('created_by', $userId)
                  ->orWhere('assigned_to', $userId);
        })
        ->with(['creator', 'assignee', 'brand'])
        ->withCount('comments')
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function getOverdueTickets()
    {
        return $this->model->overdue()
            ->with(['creator', 'assignee', 'brand'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function getTicketStats()
    {
        $stats = [
            'total' => $this->model->count(),
            'open' => $this->model->open()->count(),
            'overdue' => $this->model->overdue()->count(),
            'by_status' => [],
            'by_priority' => [],
            'recent_activity' => $this->getRecentActivity(),
        ];

        // Count by status
        foreach (Ticket::getStatuses() as $status => $label) {
            $stats['by_status'][$status] = $this->model->byStatus($status)->count();
        }

        // Count by priority
        foreach (Ticket::getPriorities() as $priority => $label) {
            $stats['by_priority'][$priority] = $this->model->byPriority($priority)->count();
        }

        return $stats;
    }

    public function searchTickets($query)
    {
        return $this->model->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%")
              ->orWhere('ticket_number', 'like', "%{$query}%");
        })
        ->with(['creator', 'assignee', 'brand'])
        ->withCount('comments')
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Get recent activity (status changes, comments, etc.)
     */
    protected function getRecentActivity($limit = 10)
    {
        return TicketStatusLog::with(['ticket', 'changedBy'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'type' => 'status_change',
                    'ticket_number' => $log->ticket->ticket_number,
                    'ticket_title' => $log->ticket->title,
                    'user' => $log->changedBy->name ?? 'System',
                    'action' => $log->status_change_description,
                    'timestamp' => $log->created_at,
                ];
            });
    }

    /**
     * Get tickets with filters
     */
    public function getFilteredTickets(array $filters = [])
    {
        $query = $this->model->query()->with(['creator', 'assignee', 'brand']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (isset($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get SLA performance data
     */
    public function getSlaPerformance()
    {
        return [
            'average_resolution_time' => $this->getAverageResolutionTime(),
            'average_response_time' => $this->getAverageResponseTime(),
            'sla_compliance_rate' => $this->getSlaComplianceRate(),
            'tickets_by_resolution_time' => $this->getTicketsByResolutionTime(),
        ];
    }

    protected function getAverageResolutionTime()
    {
        $resolvedTickets = $this->model->where('status', 'resolved')
            ->whereNotNull('resolved_at')
            ->get();

        if ($resolvedTickets->isEmpty()) {
            return 0;
        }

        $totalTime = $resolvedTickets->sum(function ($ticket) {
            return $ticket->created_at->diffInSeconds($ticket->resolved_at);
        });

        return $totalTime / $resolvedTickets->count();
    }

    protected function getAverageResponseTime()
    {
        // This would calculate time to first response (first comment)
        // For now, return a placeholder
        return 3600; // 1 hour in seconds
    }

    protected function getSlaComplianceRate()
    {
        // This would calculate SLA compliance based on your SLA rules
        // For now, return a placeholder
        return 85; // 85% compliance rate
    }

    protected function getTicketsByResolutionTime()
    {
        return [
            'under_1_hour' => $this->model->where('status', 'resolved')->count(), // Placeholder
            'under_4_hours' => $this->model->where('status', 'resolved')->count(),
            'under_24_hours' => $this->model->where('status', 'resolved')->count(),
            'over_24_hours' => $this->model->where('status', 'resolved')->count(),
        ];
    }
}
