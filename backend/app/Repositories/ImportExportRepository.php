<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ImportExportRepository implements ImportExportRepositoryInterface
{
    public function getExportData(string $type): \Illuminate\Support\Collection
    {
        return match ($type) {
            'customers' => DB::table('companies')->where('type', 'customer')->get(),
            'tickets' => DB::table('tickets')->get(),
            default => collect([]),
        };
    }

    public function importData(string $type, array $headers, array $data, int $userId): int
    {
        $imported = 0;
        foreach ($data as $row) {
            $record = array_combine($headers, $row);
            try {
                match ($type) {
                    'customers' => DB::table('companies')->insert($record),
                    'tickets' => DB::table('tickets')->insert($record),
                    default => null,
                };
                $imported++;
            } catch (\Exception $e) {
                continue;
            }
        }

        DB::table('import_history')->insert([
            'type' => $type,
            'imported_count' => $imported,
            'created_by' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $imported;
    }

    public function getImportHistory(int $userId)
    {
        return DB::table('import_history')
            ->where('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
}
