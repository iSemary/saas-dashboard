<?php

namespace Modules\Ticket\Http\Controllers\Tenant;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Ticket\Services\TicketService;
use Modules\Ticket\Entities\Ticket;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class TicketController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(TicketService $service)
    {
        $this->service = $service;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.tickets', only: ['index', 'show', 'kanban']),
            new Middleware('permission:create.tickets', only: ['create', 'store']),
            new Middleware('permission:update.tickets', only: ['edit', 'update', 'updateStatus', 'assign']),
            new Middleware('permission:delete.tickets', only: ['destroy']),
            new Middleware('permission:restore.tickets', only: ['restore']),
        ];
    }

    public function index()
    {
        
        $title = translate($this->service->model->pluralTitle);
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate($this->service->model->pluralTitle)],
        ];

        $actionButtons = [
            [
                'text' => translate("create") . " " . translate($this->service->model->singleTitle),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('tenant.tickets.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
            [
                'text' => translate("kanban_view"),
                'class' => 'btn btn-info btn-sm',
                'attr' => [
                    'href' => route('tenant.tickets.kanban'),
                ]
            ],
        ];

        $statusOptions = Ticket::getStatuses();
        $priorityOptions = Ticket::getPriorities();
        $users = \Modules\Auth\Entities\User::select('id', 'name')->get();

        return view('tenant.ticket.tickets.index', compact(
            'breadcrumbs', 
            'title', 
            'actionButtons',
            'statusOptions',
            'priorityOptions',
            'users'
        ));
    }

    public function create()
    {
        $statusOptions = Ticket::getStatuses();
        $priorityOptions = Ticket::getPriorities();
        $users = \Modules\Auth\Entities\User::select('id', 'name')->get();
        $brands = \Modules\Customer\Entities\Brand::select('id', 'name')->get();

        return view('tenant.ticket.tickets.editor', compact(
            'statusOptions',
            'priorityOptions', 
            'users',
            'brands'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'html_content' => 'nullable|string',
            'priority' => 'required|in:' . implode(',', array_keys(Ticket::getPriorities())),
            'assigned_to' => 'nullable|exists:users,id',
            'brand_id' => 'nullable|exists:brands,id',
            'due_date' => 'nullable|date|after:now',
            'tags' => 'nullable|array',
        ]);

        $data = $request->all();
        $ticket = $this->service->create($data);
        
        return $this->return(200, translate("created_successfully"), $ticket);
    }

    public function show($id)
    {
        $ticketData = $this->service->getTicketWithComments($id);
        
        if (!$ticketData) {
            return $this->return(404, translate("not_found"));
        }

        if (request()->ajax()) {
            return $this->return(200, translate("success"), $ticketData);
        }

        $title = $ticketData['ticket']->title;
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('tickets'), 'link' => route('tenant.tickets.index')],
            ['text' => $ticketData['ticket']->ticket_number],
        ];

        $timeline = $this->service->getTicketTimeline($id);
        $users = \Modules\Auth\Entities\User::select('id', 'name')->get();

        return view('tenant.ticket.tickets.show', compact(
            'ticketData',
            'timeline',
            'title',
            'breadcrumbs',
            'users'
        ));
    }

    public function edit($id)
    {
        $row = $this->service->get($id);
        if (!$row) {
            return redirect()->route('tenant.tickets.index')
                           ->with('error', translate('not_found'));
        }

        $statusOptions = Ticket::getStatuses();
        $priorityOptions = Ticket::getPriorities();
        $users = \Modules\Auth\Entities\User::select('id', 'name')->get();
        $brands = \Modules\Customer\Entities\Brand::select('id', 'name')->get();

        return view('tenant.ticket.tickets.editor', compact(
            'row',
            'statusOptions',
            'priorityOptions',
            'users',
            'brands'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'html_content' => 'nullable|string',
            'status' => 'required|in:' . implode(',', array_keys(Ticket::getStatuses())),
            'priority' => 'required|in:' . implode(',', array_keys(Ticket::getPriorities())),
            'assigned_to' => 'nullable|exists:users,id',
            'brand_id' => 'nullable|exists:brands,id',
            'due_date' => 'nullable|date',
            'tags' => 'nullable|array',
            'status_comment' => 'nullable|string',
        ]);

        $data = $request->all();
        $ticket = $this->service->update($id, $data);
        
        if (!$ticket) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("updated_successfully"), $ticket);
    }

    public function destroy($id)
    {
        $result = $this->service->delete($id);
        
        if (!$result) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("deleted_successfully"));
    }

    public function restore($id)
    {
        $result = $this->service->restore($id);
        
        if (!$result) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("restored_successfully"));
    }

    /**
     * Show Kanban board
     */
    public function kanban()
    {
        $title = translate('kanban_board');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('tickets'), 'link' => route('tenant.tickets.index')],
            ['text' => translate('kanban_board')],
        ];

        $actionButtons = [
            [
                'text' => translate("list_view"),
                'class' => 'btn btn-info btn-sm',
                'attr' => [
                    'href' => route('tenant.tickets.index'),
                ]
            ],
            [
                'text' => translate("create") . " " . translate($this->service->model->singleTitle),
                'class' => 'btn btn-success btn-sm',
                'attr' => [
                    'href' => route('tenant.tickets.create'),
                ]
            ],
        ];

        $users = \Modules\Auth\Entities\User::select('id', 'name')->get();
        $statusOptions = Ticket::getStatuses();

        return view('tenant.ticket.tickets.kanban', compact(
            'title',
            'breadcrumbs',
            'actionButtons',
            'users',
            'statusOptions'
        ));
    }

    /**
     * Get Kanban data via AJAX
     */
    public function getKanbanData()
    {
        $kanbanData = $this->service->getKanbanData();
        return $this->return(200, translate("success"), $kanbanData);
    }

    /**
     * Update ticket status (for Kanban drag & drop)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Ticket::getStatuses())),
            'comment' => 'nullable|string',
        ]);

        $ticket = $this->service->updateStatus($id, $request->status, $request->comment);
        
        if (!$ticket) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("status_updated"), $ticket);
    }

    /**
     * Assign ticket to user
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'comment' => 'nullable|string',
        ]);

        $ticket = $this->service->assignTicket($id, $request->assigned_to, $request->comment);
        
        if (!$ticket) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("ticket_assigned"), $ticket);
    }

    /**
     * Close ticket
     */
    public function close(Request $request, $id)
    {
        $request->validate([
            'comment' => 'nullable|string',
        ]);

        $ticket = $this->service->closeTicket($id, $request->comment);
        
        if (!$ticket) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("ticket_closed"), $ticket);
    }

    /**
     * Reopen ticket
     */
    public function reopen(Request $request, $id)
    {
        $request->validate([
            'comment' => 'nullable|string',
        ]);

        $ticket = $this->service->reopenTicket($id, $request->comment);
        
        if (!$ticket) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("ticket_reopened"), $ticket);
    }

    /**
     * Get my tickets
     */
    public function myTickets()
    {
        $tickets = $this->service->getMyTickets();
        return $this->return(200, translate("success"), $tickets);
    }

    /**
     * Get overdue tickets
     */
    public function overdueTickets()
    {
        $tickets = $this->service->getOverdueTickets();
        return $this->return(200, translate("success"), $tickets);
    }

    /**
     * Search tickets
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $tickets = $this->service->searchTickets($request->query);
        return $this->return(200, translate("success"), $tickets);
    }

    /**
     * Get ticket statistics
     */
    public function getStats()
    {
        $stats = $this->service->getTicketStats();
        return $this->return(200, translate("success"), $stats);
    }

    /**
     * Get dashboard data
     */
    public function getDashboardData()
    {
        $data = $this->service->getDashboardData();
        return $this->return(200, translate("success"), $data);
    }

    /**
     * Get ticket timeline
     */
    public function getTimeline($id)
    {
        $timeline = $this->service->getTicketTimeline($id);
        
        if (!$timeline) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("success"), $timeline);
    }

    /**
     * Bulk update tickets
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'integer|exists:tickets,id',
            'action' => 'required|in:assign,status,priority,delete',
            'value' => 'required',
            'comment' => 'nullable|string',
        ]);

        $results = [];

        switch ($request->action) {
            case 'assign':
                $results = $this->service->bulkAssignTickets(
                    $request->ticket_ids, 
                    $request->value, 
                    $request->comment
                );
                break;
            
            case 'status':
                $updateData = ['status' => $request->value];
                if ($request->comment) {
                    $updateData['status_comment'] = $request->comment;
                }
                $results = $this->service->bulkUpdateTickets($request->ticket_ids, $updateData);
                break;
            
            case 'priority':
                $results = $this->service->bulkUpdateTickets(
                    $request->ticket_ids, 
                    ['priority' => $request->value]
                );
                break;
            
            case 'delete':
                foreach ($request->ticket_ids as $ticketId) {
                    $this->service->delete($ticketId);
                }
                $results = $request->ticket_ids;
                break;
        }

        return $this->return(200, translate("bulk_update_completed"), [
            'updated_count' => count($results),
            'results' => $results
        ]);
    }

    /**
     * Get ticket metrics for reporting
     */
    public function getMetrics(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $metrics = $this->service->getTicketMetrics(
            $request->start_date ? \Carbon\Carbon::parse($request->start_date) : null,
            $request->end_date ? \Carbon\Carbon::parse($request->end_date) : null
        );

        return $this->return(200, translate("success"), $metrics);
    }
}

