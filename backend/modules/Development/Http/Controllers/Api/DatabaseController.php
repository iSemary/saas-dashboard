<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Development\Services\DatabaseService;

class DatabaseController extends ApiController
{
    public function __construct(protected DatabaseService $service) {}

    public function index()
    {
        $databases = [
            'landlord' => $this->service->getDatabaseStructure('landlord')
        ];

        return $this->return(200, 'Database fetched successfully', ['databases' => $databases]);
    }

    public function syncFlow(Request $request)
    {
        $validated = $request->validate([
            'nodes' => 'required|array',
            'nodes.*.connection' => 'required|string',
            'nodes.*.table' => 'required|string',
            'nodes.*.position' => 'required|array',
            'nodes.*.position.x' => 'required|numeric',
            'nodes.*.position.y' => 'required|numeric',
            'nodes.*.color' => 'required|string',
        ]);

        $this->service->syncFlow($validated['nodes']);

        return response()->json(['success' => true, 'message' => translate('message.action_completed')]);
    }
}
