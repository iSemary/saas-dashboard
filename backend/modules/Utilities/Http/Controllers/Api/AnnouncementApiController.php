<?php

namespace Modules\Utilities\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Utilities\Services\AnnouncementService;

class AnnouncementApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected AnnouncementService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'type' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);
        return $this->apiSuccess($this->service->create($validated), 'Announcement created successfully', 201);
    }

    public function show($id) { return $this->apiSuccess($this->service->findOrFail($id)); }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'body' => 'nullable|string',
            'type' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);
        $announcement = $this->service->update($id, $validated);
        return $this->apiSuccess($announcement, 'Announcement updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Announcement deleted successfully');
    }
}
