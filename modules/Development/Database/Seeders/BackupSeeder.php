<?php

namespace Modules\Development\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Development\Entities\Backup;

class BackupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $backups = [
            [
                'name' => 'Daily Backup - ' . now()->format('Y-m-d'),
                'metadata' => [
                    'type' => 'full',
                    'size' => '2.5 GB',
                    'tables' => 45,
                    'duration' => '15 minutes',
                    'status' => 'completed',
                    'created_by' => 'system',
                    'backup_path' => '/backups/daily_' . now()->format('Y-m-d') . '.sql',
                    'compression' => 'gzip',
                    'checksum' => 'sha256:abc123def456...',
                ],
            ],
            [
                'name' => 'Weekly Backup - ' . now()->subWeek()->format('Y-m-d'),
                'metadata' => [
                    'type' => 'full',
                    'size' => '2.3 GB',
                    'tables' => 45,
                    'duration' => '14 minutes',
                    'status' => 'completed',
                    'created_by' => 'system',
                    'backup_path' => '/backups/weekly_' . now()->subWeek()->format('Y-m-d') . '.sql',
                    'compression' => 'gzip',
                    'checksum' => 'sha256:def456ghi789...',
                ],
            ],
            [
                'name' => 'Monthly Backup - ' . now()->subMonth()->format('Y-m-d'),
                'metadata' => [
                    'type' => 'full',
                    'size' => '2.1 GB',
                    'tables' => 42,
                    'duration' => '12 minutes',
                    'status' => 'completed',
                    'created_by' => 'admin',
                    'backup_path' => '/backups/monthly_' . now()->subMonth()->format('Y-m-d') . '.sql',
                    'compression' => 'gzip',
                    'checksum' => 'sha256:ghi789jkl012...',
                ],
            ],
            [
                'name' => 'Incremental Backup - ' . now()->subDay()->format('Y-m-d H:i'),
                'metadata' => [
                    'type' => 'incremental',
                    'size' => '150 MB',
                    'tables' => 8,
                    'duration' => '3 minutes',
                    'status' => 'completed',
                    'created_by' => 'system',
                    'backup_path' => '/backups/incremental_' . now()->subDay()->format('Y-m-d_H-i') . '.sql',
                    'compression' => 'gzip',
                    'checksum' => 'sha256:jkl012mno345...',
                    'base_backup' => 'Daily Backup - ' . now()->subDay()->format('Y-m-d'),
                ],
            ],
            [
                'name' => 'Failed Backup - ' . now()->subDays(2)->format('Y-m-d'),
                'metadata' => [
                    'type' => 'full',
                    'size' => '0 MB',
                    'tables' => 0,
                    'duration' => '0 minutes',
                    'status' => 'failed',
                    'created_by' => 'system',
                    'error' => 'Disk space insufficient',
                    'retry_count' => 3,
                    'last_retry' => now()->subDays(2)->format('Y-m-d H:i:s'),
                ],
            ],
        ];

        foreach ($backups as $backupData) {
            Backup::create($backupData);
        }

        $this->command->info('Backups seeded successfully!');
    }
}
