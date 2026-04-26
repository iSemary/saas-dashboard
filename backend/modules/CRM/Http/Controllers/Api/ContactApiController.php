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
            $filters = $request->only(['search', 'company_id', 'assigned_to', 'brand_id']);
            $perPage = $request->get('per_page', 15);

            $contacts = $this->service->list($filters, $perPage);

            return $this->return(200, '', $contacts);
        } catch (\Exception $e) {
            return $this->return(500, 'Failed to retrieve contacts', [], ['error' => $e->getMessage()]);
        }
    }

    public function store(StoreContactRequest $request)
    {
        try {
            $data = CreateContactData::fromRequest($request);
            $contact = $this->service->create($data);

            return $this->respondCreated($contact->load(['company', 'assignedUser', 'creator'])->toArray());
        } catch (\Exception $e) {
            return $this->return(500, 'Failed to create contact', [], ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $contact = $this->service->findOrFail($id);
            return $this->respondWithArray($contact->toArray());
        } catch (\Exception $e) {
            return $this->respondNotFound(translate('message.resource_not_found'));
        }
    }

    public function update(UpdateContactRequest $request, $id)
    {
        try {
            $data = UpdateContactData::fromRequest($request);
            $contact = $this->service->update($id, $data);

            return $this->respondWithArray($contact->toArray(), 'Contact updated successfully');
        } catch (\Exception $e) {
            return $this->return(500, 'Failed to update contact', [], ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);

            return response()->json([
                'message' => translate('message.deleted_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
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
                'message' => translate('message.operation_failed'),
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
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
