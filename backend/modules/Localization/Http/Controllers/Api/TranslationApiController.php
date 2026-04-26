<?php

namespace Modules\Localization\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Localization\DTOs\CreateTranslationData;
use Modules\Localization\DTOs\UpdateTranslationData;
use Modules\Localization\Http\Requests\StoreTranslationRequest;
use Modules\Localization\Http\Requests\UpdateTranslationRequest;
use Modules\Localization\Services\TranslationService;

class TranslationApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TranslationService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'language_id']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function show($id) { return $this->apiSuccess($this->service->findOrFail($id)); }

    public function store(StoreTranslationRequest $request)
    {
        $data = CreateTranslationData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), translate('message.created_successfully'), 201);
    }

    public function update(UpdateTranslationRequest $request, $id)
    {
        $data = UpdateTranslationData::fromRequest($request);
        $this->service->update($id, $data);
        return $this->apiSuccess($this->service->findOrFail($id), translate('message.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }
}
