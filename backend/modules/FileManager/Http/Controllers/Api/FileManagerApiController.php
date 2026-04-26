<?php

namespace Modules\FileManager\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToListContents;

class FileManagerApiController extends Controller
{
    use ApiResponseEnvelope;

    public function index(Request $request)
    {
        $path = $request->get('path', '/');
        $disk = Storage::disk('public');

        try {
            $directories = $disk->directories($path);
            $files = $disk->files($path);
        } catch (UnableToListContents $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }

        $result = [
            'path' => $path,
            'directories' => array_map(fn($d) => ['name' => basename($d), 'path' => $d, 'type' => 'directory'], $directories),
            'files' => array_map(fn($f) => [
                'name' => basename($f),
                'path' => $f,
                'type' => 'file',
                'size' => $disk->size($f),
                'last_modified' => date('Y-m-d H:i:s', $disk->lastModified($f)),
            ], $files),
        ];
        return $this->apiSuccess($result);
    }
}
