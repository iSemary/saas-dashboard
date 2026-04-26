<?php

namespace Modules\Email\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Email\DTOs\CreateEmailRecipientData;
use Modules\Email\DTOs\UpdateEmailRecipientData;
use Modules\Email\Http\Requests\StoreEmailRecipientRequest;
use Modules\Email\Http\Requests\UpdateEmailRecipientRequest;
use Modules\Email\Services\EmailRecipientService;

class EmailRecipientApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected EmailRecipientService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function show($id) { return $this->apiSuccess($this->service->findOrFail($id)); }

    public function store(StoreEmailRecipientRequest $request)
    {
        $data = CreateEmailRecipientData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), translate('message.created_successfully'), 201);
    }

    public function update(UpdateEmailRecipientRequest $request, $id)
    {
        $data = UpdateEmailRecipientData::fromRequest($request);
        $this->service->update($id, $data);
        return $this->apiSuccess($this->service->findOrFail($id), translate('message.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }
}
