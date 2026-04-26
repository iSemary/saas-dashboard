<?php

namespace Modules\Geography\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Geography\DTOs\CreateCountryData;
use Modules\Geography\DTOs\UpdateCountryData;
use Modules\Geography\Http\Requests\StoreCountryRequest;
use Modules\Geography\Http\Requests\UpdateCountryRequest;
use Modules\Geography\Services\CountryService;

class CountryApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected CountryService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function show($id) { return $this->apiSuccess($this->service->findOrFail($id)); }

    public function store(StoreCountryRequest $request)
    {
        $data = CreateCountryData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), translate('message.created_successfully'), 201);
    }

    public function update(UpdateCountryRequest $request, $id)
    {
        $data = UpdateCountryData::fromRequest($request);
        $this->service->update($id, $data);
        return $this->apiSuccess($this->service->findOrFail($id), translate('message.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }
}
