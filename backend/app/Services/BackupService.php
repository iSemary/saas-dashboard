<?php

namespace App\Services;

use App\Repositories\BackupRepositoryInterface;

class BackupService
{
    public function __construct(protected BackupRepositoryInterface $repository) {}

    public function list(): array
    {
        return $this->repository->listBackups();
    }

    public function create(string $type = 'database'): array
    {
        return $this->repository->createBackup($type);
    }

    public function download(string $filename): ?string
    {
        return $this->repository->getBackupPath($filename);
    }

    public function restore(string $filename): bool
    {
        return $this->repository->restoreBackup($filename);
    }

    public function delete(string $filename): bool
    {
        return $this->repository->deleteBackup($filename);
    }
}
