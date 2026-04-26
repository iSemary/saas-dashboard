<?php

namespace Modules\Email\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Email\DTOs\CreateEmailGroupData;
use Modules\Email\DTOs\UpdateEmailGroupData;
use Modules\Email\Http\Requests\StoreEmailGroupRequest;
use Modules\Email\Http\Requests\UpdateEmailGroupRequest;
use Modules\Email\Services\EmailGroupService;

class EmailGroupApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected EmailGroupService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function show($id) { return $this->apiSuccess($this->service->findOrFail($id)); }

    public function store(StoreEmailGroupRequest $request)
    {
        $data = CreateEmailGroupData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), translate('message.created_successfully'), 201);
    }

    public function update(UpdateEmailGroupRequest $request, $id)
    {
        $data = UpdateEmailGroupData::fromRequest($request);
        $this->service->update($id, $data);
        return $this->apiSuccess($this->service->findOrFail($id), translate('message.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }
}
