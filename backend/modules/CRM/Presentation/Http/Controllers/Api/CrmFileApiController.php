<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\CRM\Infrastructure\Persistence\CrmFileRepositoryInterface;

class CrmFileApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly CrmFileRepositoryInterface $files) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [];
            if ($request->has('related_type') && $request->has('related_id')) {
                $filters['related_type'] = $request->input('related_type');
                $filters['related_id'] = $request->input('related_id');
            }
            return $this->apiPaginated($this->files->paginate($filters, (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve files', 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240',
                'related_type' => 'required|string',
                'related_id' => 'required|integer',
            ]);
            
            $file = $request->file('file');
            $path = $file->store('crm/files', 'public');
            
            $data = [
                'name' => $file->getClientOriginalName(),
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'path' => $path,
                'disk' => 'public',
                'related_type' => $request->input('related_type'),
                'related_id' => $request->input('related_id'),
                'created_by' => auth()->id(),
            ];
            
            $crmFile = $this->files->create($data);
            return $this->apiSuccess($crmFile, 'File uploaded', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to upload file', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->files->findOrFail($id));
        } catch (\Throwable $e) {
            return $this->apiError('File not found', 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->files->delete($id);
            return $this->apiSuccess(null, 'File deleted');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete file', 500, $e->getMessage());
        }
    }

    public function download(int $id)
    {
        try {
            $file = $this->files->findOrFail($id);
            return Storage::disk($file->disk)->download($file->path, $file->file_name);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to download file'], 500);
        }
    }

    public function getForRelated(string $type, int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->files->getForRelated($type, $id));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get files', 500, $e->getMessage());
        }
    }
}
