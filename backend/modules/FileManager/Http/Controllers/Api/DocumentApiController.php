<?php

namespace Modules\FileManager\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\FileManager\Services\DocumentService;
use Illuminate\Support\Facades\Storage;

class DocumentApiController extends ApiController
{
    public function __construct(protected DocumentService $service) {}

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['folder_id', 'search']);
            $perPage = $request->get('per_page', 20);
            $files = $this->service->list($filters, $perPage);

            return response()->json([
                'data' => $files->items(),
                'current_page' => $files->currentPage(),
                'last_page' => $files->lastPage(),
                'per_page' => $files->perPage(),
                'total' => $files->total(),
                'from' => $files->firstItem(),
                'to' => $files->lastItem(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve documents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240',
                'folder_id' => 'nullable|exists:folders,id',
                'access_level' => 'nullable|in:private,public',
            ]);

            $data = $request->only(['folder_id', 'access_level']);
            $document = $this->service->upload($data, $request->file('file'));

            return response()->json([
                'data' => $document->load('folder'),
                'message' => 'File uploaded successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $file = $this->service->findOrFail($id);
            return response()->json(['data' => $file]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'File not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'original_name' => 'sometimes|string|max:255',
                'access_level' => 'sometimes|in:private,public',
                'status' => 'sometimes|in:active,inactive,archived',
                'folder_id' => 'nullable|exists:folders,id',
            ]);

            $file = $this->service->update($id, $validated);

            return response()->json([
                'data' => $file,
                'message' => 'File updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['message' => 'File deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function download($id)
    {
        try {
            $file = $this->service->findOrFail($id);
            $filePath = $this->service->download($id);
            return Storage::disk('public')->download(
                str_replace(Storage::disk('public')->path('') , '', $filePath),
                $file->original_name
            );
        } catch (\Exception $e) {
            $status = str_contains($e->getMessage(), 'permission') ? 403 :
                      (str_contains($e->getMessage(), 'not found') ? 404 : 500);
            return response()->json([
                'message' => 'Failed to download file',
                'error' => $e->getMessage()
            ], $status);
        }
    }

    public function versions($id)
    {
        try {
            $this->service->findOrFail($id);
            return response()->json(['data' => []]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve versions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:files,id'
            ]);

            $deleted = $this->service->bulkDelete($request->ids);

            return response()->json([
                'message' => "{$deleted} files deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete files',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
