<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\TicketRepositoryInterface;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected TicketRepositoryInterface $ticketRepository;

    public function __construct(TicketRepositoryInterface $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Get ticket detail with all relations
     */
    public function show(int $id)
    {
        try {
            $ticket = $this->ticketRepository->getDetailWithRelations($id);

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }

            // Get comment stats
            $commentStats = [
                'total_comments' => $ticket->comments()->count(),
                'public_comments' => $ticket->comments()->where('is_private', false)->count(),
                'private_comments' => $ticket->comments()->where('is_private', true)->count(),
            ];

            // Get activity timeline
            $timeline = $this->ticketRepository->getActivityTimeline($ticket);

            // Get SLA data
            $slaData = $this->ticketRepository->getSLAMetrics($ticket);

            return response()->json([
                'success' => true,
                'data' => [
                    'ticket' => $ticket,
                    'comments' => $ticket->comments()->with('user', 'attachments')->orderBy('created_at', 'desc')->get(),
                    'comment_stats' => $commentStats,
                    'timeline' => $timeline,
                    'sla_data' => $slaData,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Add comment to ticket
     */
    public function addComment(Request $request, int $id)
    {
        $request->validate([
            'comment' => 'required|string',
            'is_private' => 'boolean',
            'attachments' => 'array',
            'attachments.*' => 'file|max:10240',
        ]);

        try {
            $data = [
                'comment' => $request->input('comment'),
                'is_private' => $request->input('is_private', false),
                'user_id' => auth()->id(),
                'attachments' => $request->file('attachments', []),
            ];

            $comment = $this->ticketRepository->addComment($id, $data);

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $comment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Assign ticket to user
     */
    public function assign(Request $request, int $id)
    {
        $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
        ]);

        try {
            $ticket = $this->ticketRepository->assignToUser(
                $id,
                $request->input('assigned_to')
            );

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $ticket,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Change ticket status
     */
    public function changeStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|string|in:open,in_progress,waiting,resolved,closed',
            'comment' => 'string|nullable',
        ]);

        try {
            $ticket = $this->ticketRepository->changeStatus(
                $id,
                $request->input('status'),
                $request->input('comment')
            );

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $ticket,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Change ticket priority
     */
    public function changePriority(Request $request, int $id)
    {
        $request->validate([
            'priority' => 'required|string|in:low,medium,high,urgent',
        ]);

        try {
            $ticket = $this->ticketRepository->changePriority(
                $id,
                $request->input('priority')
            );

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $ticket,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Close ticket
     */
    public function close(Request $request, int $id)
    {
        try {
            $ticket = $this->ticketRepository->close($id);

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $ticket,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Reopen ticket
     */
    public function reopen(Request $request, int $id)
    {
        try {
            $ticket = $this->ticketRepository->reopen($id);

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $ticket,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get tickets for kanban view grouped by status
     */
    public function kanban(Request $request)
    {
        $request->validate([
            'status' => 'string|in:open,in_progress,waiting,resolved,closed',
            'assignee_id' => 'integer|nullable',
            'priority' => 'string|in:low,medium,high,urgent|nullable',
        ]);

        try {
            $filters = [
                'assignee_id' => $request->input('assignee_id'),
                'priority' => $request->input('priority'),
            ];

            // If specific status requested
            if ($request->has('status')) {
                $tickets = $this->ticketRepository->getByStatus($request->input('status'), $filters);
                return response()->json([
                    'success' => true,
                    'data' => [
                        $request->input('status') => $tickets,
                    ],
                ]);
            }

            // Return all columns
            $columns = ['open', 'in_progress', 'waiting', 'resolved', 'closed'];
            $data = [];

            foreach ($columns as $status) {
                $data[$status] = $this->ticketRepository->getByStatus($status, $filters);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
