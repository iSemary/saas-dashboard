<?php

namespace Modules\FileManager\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class FileApiController extends Controller
{
    use ApiResponseEnvelope;

    public function index(Request $request)
    {
        $disk = Storage::disk('public');
        $allFiles = $disk->allFiles();
        $files = array_map(fn($f) => [
            'id' => md5($f),
            'name' => basename($f),
            'path' => $f,
            'size' => $disk->size($f),
            'mime_type' => mime_content_type($disk->path($f)) ?: 'application/octet-stream',
        ], $allFiles);
        return $this->apiSuccess($files);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240',
        ]);
        $path = $request->file('file')->store('uploads', 'public');
        return $this->apiSuccess(['name' => basename($path), 'path' => $path], translate('message.action_completed'), 201);
    }

    public function destroy($id)
    {
        // Find file by hash — simplified approach
        $disk = Storage::disk('public');
        $allFiles = $disk->allFiles();
        foreach ($allFiles as $f) {
            if (md5($f) === $id) {
                $disk->delete($f);
                return $this->apiSuccess(null, translate('message.deleted_successfully'));
            }
        }
        return $this->apiError(translate('message.resource_not_found'), 404);
    }
}
