<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BulkAction\BulkActionService;
use App\Services\BulkAction\Actions\DeleteAction;
use App\Services\BulkAction\Actions\ActivateAction;
use App\Services\BulkAction\Actions\DeactivateAction;
use Illuminate\Http\Request;

class BulkActionController extends Controller
{
    protected BulkActionService $bulkActionService;

    public function __construct()
    {
        $this->bulkActionService = new BulkActionService();
        
        // Register default actions
        $this->bulkActionService->registerAction('delete', new DeleteAction());
        $this->bulkActionService->registerAction('activate', new ActivateAction());
        $this->bulkActionService->registerAction('deactivate', new DeactivateAction());
    }

    /**
     * Get available actions for entity
     */
    public function actions(string $entity)
    {
        $actions = $this->bulkActionService->getAvailableActions($entity);

        return response()->json([
            'success' => true,
            'data' => $actions,
        ]);
    }

    /**
     * Execute bulk action
     */
    public function execute(Request $request, string $entity)
    {
        $request->validate([
            'action' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'params' => 'array',
        ]);

        $action = $request->input('action');
        $ids = $request->input('ids');
        $params = $request->input('params', []);
        $userId = auth()->id() ?? 0;

        try {
            // Add model class to params based on entity
            $modelClass = $this->getModelClass($entity);
            if ($modelClass) {
                $params['model_class'] = $modelClass;
            }

            $result = $this->bulkActionService->execute($action, $entity, $ids, $params, $userId);

            return response()->json([
                'success' => $result->success,
                'data' => $result->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Preview bulk action
     */
    public function preview(Request $request, string $entity)
    {
        $request->validate([
            'action' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $action = $request->input('action');
        $ids = $request->input('ids');

        try {
            $result = $this->bulkActionService->preview($action, $entity, $ids);

            return response()->json([
                'success' => true,
                'data' => $result->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Export selected items
     */
    public function export(Request $request, string $entity)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'format' => 'string|in:csv,xlsx',
        ]);

        $ids = $request->input('ids');
        $format = $request->input('format', 'xlsx');

        try {
            $result = $this->bulkActionService->export($entity, $ids, $format);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['errors'][0] ?? 'Export failed',
                ], 400);
            }

            // TODO: Generate and return actual export file
            return response()->json([
                'success' => true,
                'count' => $result['count'],
                'message' => 'Export ready for download',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get model class for entity
     */
    protected function getModelClass(string $entity): ?string
    {
        $modelMap = [
            'branches' => 'App\\Models\\Branch',
            'brands' => 'App\\Models\\Brand',
            'users' => 'App\\Models\\User',
            'tenants' => 'App\\Models\\Tenant',
            'tickets' => 'App\\Models\\Ticket',
        ];

        return $modelMap[$entity] ?? null;
    }
}
