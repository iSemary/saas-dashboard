<?php

namespace Modules\FileManager\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\FileManager\DTOs\CreateFolderData;
use Modules\FileManager\DTOs\UpdateFolderData;
use Modules\FileManager\Http\Requests\StoreFolderRequest;
use Modules\FileManager\Http\Requests\UpdateFolderRequest;
use Modules\FileManager\Services\FolderService;

class FolderApiController extends ApiController
{
    public function __construct(protected FolderService $service) {}

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['parent_id', 'search']);
            $folders = $this->service->list($filters);
            return response()->json(['data' => $folders]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreFolderRequest $request)
    {
        try {
            $data = CreateFolderData::fromRequest($request);
            $folder = $this->service->create($data);

            return response()->json([
                'data' => $folder->load(['files', 'parent']),
                'message' => translate('message.created_successfully')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $folder = $this->service->findOrFail($id);
            return response()->json(['data' => $folder]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.resource_not_found'),
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(UpdateFolderRequest $request, $id)
    {
        try {
            $data = UpdateFolderData::fromRequest($request);
            $folder = $this->service->update($id, $data);

            return response()->json([
                'data' => $folder,
                'message' => translate('message.updated_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['message' => translate('message.deleted_successfully')]);
        } catch (\Exception $e) {
            $status = str_contains($e->getMessage(), 'Cannot delete') ? 400 : 500;
            return response()->json([
                'message' => $e->getMessage() ?: 'Failed to delete folder',
                'error' => $e->getMessage()
            ], $status);
        }
    }
}
