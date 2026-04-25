<?php

namespace Modules\CRM\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Modules\CRM\DTOs\CreateContactData;
use Modules\CRM\DTOs\UpdateContactData;
use Modules\CRM\Http\Requests\StoreContactRequest;
use Modules\CRM\Http\Requests\UpdateContactRequest;
use Modules\CRM\Services\ContactService;

class ContactApiController extends ApiController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.crm.contacts', only: ['index', 'show', 'activity']),
            new Middleware('permission:create.crm.contacts', only: ['store']),
            new Middleware('permission:update.crm.contacts', only: ['update']),
            new Middleware('permission:delete.crm.contacts', only: ['destroy', 'bulkDelete']),
        ];
    }

    public function __construct(protected ContactService $service) {}

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'company_id', 'assigned_to']);
            $perPage = $request->get('per_page', 15);

            $contacts = $this->service->list($filters, $perPage);

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

    public function store(StoreContactRequest $request)
    {
        try {
            $data = CreateContactData::fromRequest($request);
            $contact = $this->service->create($data);

            return response()->json([
                'data' => $contact->load(['company', 'assignedUser', 'creator']),
                'message' => 'Contact created successfully'
            ], 201);
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
            $contact = $this->service->findOrFail($id);
            return response()->json(['data' => $contact]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Contact not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(UpdateContactRequest $request, $id)
    {
        try {
            $data = UpdateContactData::fromRequest($request);
            $contact = $this->service->update($id, $data);

            return response()->json([
                'data' => $contact,
                'message' => 'Contact updated successfully'
            ]);
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
            $this->service->delete($id);

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
            $audits = $this->service->getActivity($id);

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

            $deleted = $this->service->bulkDelete($request->ids);

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
