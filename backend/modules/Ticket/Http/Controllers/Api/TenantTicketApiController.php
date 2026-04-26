<?php

namespace Modules\Ticket\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Ticket\Services\TicketService;

class TenantTicketApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TicketService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'priority' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'category_id' => 'nullable|integer',
            'assigned_to' => 'nullable|integer',
        ]);
        return $this->apiSuccess($this->service->create($validated), translate('message.created_successfully'), 201);
    }

    public function show($id) { return $this->apiSuccess($this->service->findOrFail($id)); }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'subject' => 'sometimes|required|string|max:255',
            'body' => 'nullable|string',
            'priority' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'category_id' => 'nullable|integer',
            'assigned_to' => 'nullable|integer',
        ]);
        $this->service->update($id, $validated);
        return $this->apiSuccess($this->service->findOrFail($id), translate('message.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }

    public function kanbanData()
    {
        return $this->apiSuccess($this->service->kanbanData());
    }

    public function stats()
    {
        return $this->apiSuccess($this->service->stats());
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate(['status' => 'required|string|max:50']);
        $ticket = $this->service->updateStatus($id, $validated['status']);
        return $this->apiSuccess($ticket, translate('message.updated_successfully'));
    }

    public function assign(Request $request, $id)
    {
        $validated = $request->validate(['assigned_to' => 'required|integer']);
        $ticket = $this->service->assign($id, $validated['assigned_to']);
        return $this->apiSuccess($ticket, translate('message.action_completed'));
    }
}
