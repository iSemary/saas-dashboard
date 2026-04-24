<?php

namespace Modules\Ticket\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Ticket\DTOs\CreateTicketData;
use Modules\Ticket\DTOs\UpdateTicketData;
use Modules\Ticket\Http\Requests\StoreTicketRequest;
use Modules\Ticket\Http\Requests\UpdateTicketRequest;
use Modules\Ticket\Services\TicketService;

class TicketApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TicketService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function show($id) { return $this->apiSuccess($this->service->findOrFail($id)); }

    public function store(StoreTicketRequest $request)
    {
        $data = CreateTicketData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'Ticket created successfully', 201);
    }

    public function update(UpdateTicketRequest $request, $id)
    {
        $data = UpdateTicketData::fromRequest($request);
        $this->service->update($id, $data);
        return $this->apiSuccess($this->service->findOrFail($id), 'Ticket updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Ticket deleted successfully');
    }
}
