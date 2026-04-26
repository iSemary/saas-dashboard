<?php

namespace Modules\FileManager\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\FileManager\Services\MediaService;

class MediaApiController extends ApiController
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Upload a single file.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            $maxSize = config('filemanager.media.max_photo_size', 10240);
            $request->validate([
                'file' => 'required|file|max:' . $maxSize,
                'folder_id' => 'nullable|integer|exists:folders,id',
                'access_level' => 'nullable|in:private,public',
            ]);

            $file = $request->file('file');
            $folderId = $request->input('folder_id');
            $accessLevel = $request->input('access_level', 'public');

            $media = $this->mediaService->upload($file, $folderId, $accessLevel);

            return response()->json([
                'status' => 201,
                'success' => true,
                'message' => translate('message.action_completed'),
                'data' => $media,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'success' => false,
                'message' => translate('message.validation_failed'),
                'errors' => $e->errors(),
            ], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 422,
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload multiple files in bulk.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadBulk(Request $request)
    {
        try {
            $maxBulkCount = config('filemanager.media.max_bulk_count', 10);
            $maxSize = config('filemanager.media.max_photo_size', 10240);

            $request->validate([
                'files' => 'required|array|min:1|max:' . $maxBulkCount,
                'files.*' => 'required|file|max:' . $maxSize,
                'folder_id' => 'nullable|integer|exists:folders,id',
                'access_level' => 'nullable|in:private,public',
            ]);

            $files = $request->file('files');
            $folderId = $request->input('folder_id');
            $accessLevel = $request->input('access_level', 'public');

            // Filter out any null/empty entries
            $validFiles = array_filter($files, fn($f) => $f && $f->isValid());

            if (empty($validFiles)) {
                return response()->json([
                    'status' => 422,
                    'success' => false,
                    'message' => translate('message.validation_failed'),
                ], 422);
            }

            $media = $this->mediaService->uploadBulk($validFiles, $folderId, $accessLevel);

            return response()->json([
                'status' => 201,
                'success' => true,
                'message' => translate('message.action_completed'),
                'data' => $media,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'success' => false,
                'message' => translate('message.validation_failed'),
                'errors' => $e->errors(),
            ], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 422,
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a media record by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $media = $this->mediaService->findById($id);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => translate('message.action_completed'),
                'data' => $media,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => translate('message.resource_not_found'),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a media record by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $this->mediaService->delete($id);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => translate('message.deleted_successfully'),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => translate('message.resource_not_found'),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
