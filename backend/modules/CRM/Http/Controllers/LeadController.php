<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\CRM\Services\LeadService;
use Modules\CRM\Models\Lead;

class LeadController extends Controller
{
    protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'source', 'assigned_to', 'search', 'date_from', 'date_to']);
        $leads = $this->leadService->getAllLeads($filters);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $leads->items(),
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
                'from' => $leads->firstItem(),
                'to' => $leads->lastItem(),
                'statistics' => $this->leadService->getLeadStatistics(),
            ]);
        }

        return view('crm::leads.index', compact('leads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crm::leads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:new,contacted,qualified,unqualified,converted',
            'source' => 'nullable|in:website,phone,email,social,referral,advertisement,other',
            'expected_revenue' => 'nullable|numeric|min:0',
            'expected_close_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            $lead = $this->leadService->createLead($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully',
                'data' => $lead,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create lead: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $lead = $this->leadService->getLeadById($id);

        if (!$lead) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lead not found',
                ], 404);
            }
            abort(404, 'Lead not found');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $lead,
            ]);
        }

        return view('crm::leads.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $lead = $this->leadService->getLeadById($id);

        if (!$lead) {
            abort(404, 'Lead not found');
        }

        return view('crm::leads.edit', compact('lead'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:new,contacted,qualified,unqualified,converted',
            'source' => 'nullable|in:website,phone,email,social,referral,advertisement,other',
            'expected_revenue' => 'nullable|numeric|min:0',
            'expected_close_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            $success = $this->leadService->updateLead($id, $request->all());

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead updated successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lead: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->leadService->deleteLead($id);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead deleted successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete lead: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Convert lead to opportunity.
     */
    public function convert(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stage' => 'nullable|in:prospecting,qualification,proposal,negotiation,closed_won,closed_lost',
            'probability' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $opportunity = $this->leadService->convertLeadToOpportunity($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Lead converted to opportunity successfully',
                'data' => $opportunity,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to convert lead: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update lead status.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:new,contacted,qualified,unqualified,converted',
        ]);

        try {
            $success = $this->leadService->updateLeadStatus($id, $request->status);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead status updated successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lead status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assign lead to user.
     */
    public function assign(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        try {
            $success = $this->leadService->assignLead($id, $request->assigned_to);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead assigned successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign lead: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search leads.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        try {
            $leads = $this->leadService->searchLeads($request->query('query'));

            return response()->json([
                'success' => true,
                'data' => $leads,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search leads: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get lead statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->leadService->getLeadStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage(),
            ], 500);
        }
    }
}
