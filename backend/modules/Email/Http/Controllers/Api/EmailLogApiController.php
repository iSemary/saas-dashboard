<?php

namespace Modules\Email\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Email\Services\EmailService;

class EmailLogApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected EmailService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Log entry deleted successfully');
    }
}
