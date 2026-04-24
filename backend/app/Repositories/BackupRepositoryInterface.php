<?php

namespace App\Repositories;

interface BackupRepositoryInterface
{
    public function listBackups(): array;

    public function createBackup(string $type): array;

    public function getBackupPath(string $filename): ?string;

    public function restoreBackup(string $filename): bool;

    public function deleteBackup(string $filename): bool;
}
