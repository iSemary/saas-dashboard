<?php

namespace Modules\CRM\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\CRM\Models\Contact;
use Modules\CRM\Services\ContactService;
use OwenIt\Auditing\Models\Audit;

class ContactApiController extends ApiController
{
    protected $service;

    public function __construct(ContactService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            
            $query = Contact::with(['company', 'assignedUser', 'creator']);

            // Search
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Filter by company
            if ($request->has('company_id') && $request->company_id) {
                $query->where('company_id', $request->company_id);
            }

            // Filter by assigned user
            if ($request->has('assigned_to') && $request->assigned_to) {
                $query->where('assigned_to', $request->assigned_to);
            }

            $contacts = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'data' => [
                    'data' => $contacts->items(),
                    'current_page' => $contacts->currentPage(),
                    'last_page' => $contacts->lastPage(),
                    'per_page' => $contacts->perPage(),
                    'total' => $contacts->total(),
                    'from' => $contacts->firstItem(),
                    'to' => $contacts->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve contacts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'mobile' => 'nullable|string|max:255',
                'title' => 'nullable|string|max:255',
                'company_id' => 'nullable|exists:companies,id',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'birthday' => 'nullable|date',
                'notes' => 'nullable|string',
                'type' => 'nullable|in:individual,company',
                'assigned_to' => 'nullable|exists:users,id',
                'custom_fields' => 'nullable|array',
            ]);

            $validated['created_by'] = auth()->id();
            $validated['type'] = $validated['type'] ?? 'individual';

            $contact = Contact::create($validated);

            return response()->json([
                'data' => $contact->load(['company', 'assignedUser', 'creator']),
                'message' => 'Contact created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create contact',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $contact = Contact::with(['company', 'assignedUser', 'creator', 'opportunities'])->findOrFail($id);
            return response()->json(['data' => $contact]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Contact not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $contact = Contact::findOrFail($id);

            $validated = $request->validate([
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'mobile' => 'nullable|string|max:255',
                'title' => 'nullable|string|max:255',
                'company_id' => 'nullable|exists:companies,id',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'birthday' => 'nullable|date',
                'notes' => 'nullable|string',
                'type' => 'nullable|in:individual,company',
                'assigned_to' => 'nullable|exists:users,id',
                'custom_fields' => 'nullable|array',
            ]);

            $contact->update($validated);

            return response()->json([
                'data' => $contact->load(['company', 'assignedUser', 'creator']),
                'message' => 'Contact updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update contact',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();

            return response()->json([
                'message' => 'Contact deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete contact',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function activity($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            
            $audits = Audit::where('auditable_type', Contact::class)
                ->where('auditable_id', $id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'data' => [
                    'data' => $audits->items(),
                    'current_page' => $audits->currentPage(),
                    'last_page' => $audits->lastPage(),
                    'per_page' => $audits->perPage(),
                    'total' => $audits->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:contacts,id'
            ]);

            $deleted = Contact::whereIn('id', $request->ids)->delete();

            return response()->json([
                'message' => "{$deleted} contacts deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete contacts',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
