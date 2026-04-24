<?php

namespace Modules\Email\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Email\DTOs\CreateEmailSubscriberData;
use Modules\Email\DTOs\UpdateEmailSubscriberData;
use Modules\Email\Http\Requests\StoreEmailSubscriberRequest;
use Modules\Email\Http\Requests\UpdateEmailSubscriberRequest;
use Modules\Email\Services\EmailSubscriberService;

class EmailSubscriberApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected EmailSubscriberService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function show($id) { return $this->apiSuccess($this->service->findOrFail($id)); }

    public function store(StoreEmailSubscriberRequest $request)
    {
        $data = CreateEmailSubscriberData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'Subscriber created successfully', 201);
    }

    public function update(UpdateEmailSubscriberRequest $request, $id)
    {
        $data = UpdateEmailSubscriberData::fromRequest($request);
        $this->service->update($id, $data);
        return $this->apiSuccess($this->service->findOrFail($id), 'Subscriber updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Subscriber deleted successfully');
    }
}
