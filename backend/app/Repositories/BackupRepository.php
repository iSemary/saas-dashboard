<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

class BackupRepository implements BackupRepositoryInterface
{
    public function listBackups(): array
    {
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

        return $backupList;
    }

    public function createBackup(string $type): array
    {
        $filename = "backup-{$type}-" . now()->format('Y-m-d-His') . ".sql";
        $path = storage_path("app/backups/{$filename}");

        if ($type === 'database' || $type === 'full') {
            $database = config('database.connections.tenant.database');
            $username = config('database.connections.tenant.username');
            $password = config('database.connections.tenant.password');
            $host = config('database.connections.tenant.host');

            $command = "mysqldump -h {$host} -u {$username} -p{$password} {$database} > {$path}";
            exec($command);
        }

        return ['filename' => $filename];
    }

    public function getBackupPath(string $filename): ?string
    {
        $path = storage_path("app/backups/{$filename}");

        return file_exists($path) ? $path : null;
    }

    public function restoreBackup(string $filename): bool
    {
        $path = storage_path("app/backups/{$filename}");

        if (!file_exists($path)) {
            return false;
        }

        $database = config('database.connections.tenant.database');
        $username = config('database.connections.tenant.username');
        $password = config('database.connections.tenant.password');
        $host = config('database.connections.tenant.host');

        $command = "mysql -h {$host} -u {$username} -p{$password} {$database} < {$path}";
        exec($command);

        return true;
    }

    public function deleteBackup(string $filename): bool
    {
        $path = storage_path("app/backups/{$filename}");

        if (file_exists($path)) {
            unlink($path);
        }

        return true;
    }
}
