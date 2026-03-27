<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class BackupApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $backups = Storage::disk('local')->files('backups');
            $backupList = array_map(function ($file) {
                return [
                    'name' => basename($file),
                    'size' => Storage::disk('local')->size($file),
                    'created_at' => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($file)),
                ];
            }, $backups);

            usort($backupList, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

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
            $type = $request->get('type', 'database'); // database, files, full

            // Create backup using Laravel backup package or custom logic
            $filename = "backup-{$type}-" . now()->format('Y-m-d-His') . ".sql";
            $path = storage_path("app/backups/{$filename}");

            if ($type === 'database' || $type === 'full') {
                // Export database
                $database = config('database.connections.tenant.database');
                $username = config('database.connections.tenant.username');
                $password = config('database.connections.tenant.password');
                $host = config('database.connections.tenant.host');

                $command = "mysqldump -h {$host} -u {$username} -p{$password} {$database} > {$path}";
                exec($command);
            }

            return response()->json([
                'message' => 'Backup created successfully',
                'data' => ['filename' => $filename]
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
            $path = storage_path("app/backups/{$filename}");
            if (!file_exists($path)) {
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

            $path = storage_path("app/backups/{$filename}");
            if (!file_exists($path)) {
                return response()->json(['message' => 'Backup not found'], 404);
            }

            // Restore database
            $database = config('database.connections.tenant.database');
            $username = config('database.connections.tenant.username');
            $password = config('database.connections.tenant.password');
            $host = config('database.connections.tenant.host');

            $command = "mysql -h {$host} -u {$username} -p{$password} {$database} < {$path}";
            exec($command);

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
            $path = storage_path("app/backups/{$filename}");
            if (file_exists($path)) {
                unlink($path);
            }

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
