<?php

namespace App\Repositories;

interface ImportExportRepositoryInterface
{
    public function getExportData(string $type): \Illuminate\Support\Collection;

    public function importData(string $type, array $headers, array $data, int $userId): int;

    public function getImportHistory(int $userId);
}
