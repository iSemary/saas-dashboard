<?php

namespace Modules\Utilities\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Utilities\DTOs\CreateCurrencyData;
use Modules\Utilities\DTOs\UpdateCurrencyData;
use Modules\Utilities\Http\Requests\StoreCurrencyRequest;
use Modules\Utilities\Http\Requests\UpdateCurrencyRequest;
use Modules\Utilities\Services\CurrencyService;

class CurrencyApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected CurrencyService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function show($id) { return $this->apiSuccess($this->service->findOrFail($id)); }

    public function store(StoreCurrencyRequest $request)
    {
        $data = CreateCurrencyData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'Currency created successfully', 201);
    }

    public function update(UpdateCurrencyRequest $request, $id)
    {
        $data = UpdateCurrencyData::fromRequest($request);
        return $this->apiSuccess($this->service->update($id, $data), 'Currency updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Currency deleted successfully');
    }
}
