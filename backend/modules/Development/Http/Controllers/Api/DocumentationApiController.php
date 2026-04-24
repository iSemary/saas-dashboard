<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Development\DTOs\CreateDocumentationData;
use Modules\Development\DTOs\UpdateDocumentationData;
use Modules\Development\Http\Requests\StoreDocumentationRequest;
use Modules\Development\Http\Requests\UpdateDocumentationRequest;
use Modules\Development\Services\DocumentationService;

class DocumentationApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected DocumentationService $service) {}

    public function index(Request $request)
    {
        return $this->apiPaginated($this->service->list($request->get('per_page', 50)));
    }

    public function store(StoreDocumentationRequest $request)
    {
        $data = CreateDocumentationData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'Documentation created successfully', 201);
    }

    public function update(UpdateDocumentationRequest $request, $id)
    {
        $data = UpdateDocumentationData::fromRequest($request);
        return $this->apiSuccess($this->service->update($id, $data), 'Documentation updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Documentation deleted successfully');
    }
}
