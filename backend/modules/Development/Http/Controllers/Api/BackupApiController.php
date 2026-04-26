<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupApiController extends Controller
{
    use ApiResponseEnvelope;

    public function index(Request $request)
    {
        $disk = Storage::disk('local');
        $backups = [];
        $path = 'backups';
        if ($disk->exists($path)) {
            foreach ($disk->files($path) as $file) {
                $backups[] = [
                    'id' => basename($file),
                    'name' => basename($file),
                    'size' => $disk->size($file),
                    'created_at' => date('Y-m-d H:i:s', $disk->lastModified($file)),
                ];
            }
        }
        return $this->apiSuccess($backups);
    }

    public function store(Request $request)
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            return $this->apiSuccess(null, translate('message.created_successfully'));
        } catch (\Exception $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function download($id)
    {
        $disk = Storage::disk('local');
        $path = 'backups/' . $id;
        if (!$disk->exists($path)) {
            return $this->apiError(translate('message.resource_not_found'), 404);
        }
        return $disk->download($path);
    }

    public function destroy($id)
    {
        $disk = Storage::disk('local');
        $path = 'backups/' . $id;
        if ($disk->exists($path)) {
            $disk->delete($path);
        }
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }
}
