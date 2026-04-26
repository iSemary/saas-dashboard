<?php

namespace Modules\Localization\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Localization\DTOs\CreateLanguageData;
use Modules\Localization\DTOs\UpdateLanguageData;
use Modules\Localization\Http\Requests\StoreLanguageRequest;
use Modules\Localization\Http\Requests\UpdateLanguageRequest;
use Modules\Localization\Services\LanguageService;

class LanguageApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected LanguageService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function show($id) { return $this->apiSuccess($this->service->findOrFail($id)); }

    public function store(StoreLanguageRequest $request)
    {
        $data = CreateLanguageData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), translate('message.created_successfully'), 201);
    }

    public function update(UpdateLanguageRequest $request, $id)
    {
        $data = UpdateLanguageData::fromRequest($request);
        $this->service->update($id, $data);
        return $this->apiSuccess($this->service->findOrFail($id), translate('message.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }
}
