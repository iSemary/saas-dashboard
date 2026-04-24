<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\Request;

class BackupApiController extends Controller
{
    public function __construct(protected BackupService $backupService) {}

    public function index(Request $request)
    {
        try {
            $backupList = $this->backupService->list();
            return response()->json(['data' => $backupList]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve backups',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $type = $request->get('type', 'database');
            $result = $this->backupService->create($type);

            return response()->json([
                'message' => 'Backup created successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create backup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function download($filename)
    {
        try {
            $path = $this->backupService->download($filename);

            if (!$path) {
                return response()->json(['message' => 'Backup not found'], 404);
            }

            return response()->download($path);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to download backup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function restore(Request $request, $filename)
    {
        try {
            $request->validate([
                'confirm' => 'required|boolean|accepted',
            ]);

            $restored = $this->backupService->restore($filename);

            if (!$restored) {
                return response()->json(['message' => 'Backup not found'], 404);
            }

            return response()->json([
                'message' => 'Backup restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to restore backup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($filename)
    {
        try {
            $this->backupService->delete($filename);

            return response()->json([
                'message' => 'Backup deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete backup',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
