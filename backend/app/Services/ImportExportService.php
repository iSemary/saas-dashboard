<?php

namespace App\Services;

use App\Repositories\ImportExportRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportService
{
    public function __construct(protected ImportExportRepositoryInterface $repository) {}

    public function getExportData(string $type)
    {
        return $this->repository->getExportData($type);
    }

    public function exportToCsv(string $type, $data)
    {
        $filename = "export-{$type}-" . now()->format('Y-m-d') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            if (!empty($data)) {
                fputcsv($file, array_keys((array) $data[0]));
                foreach ($data as $row) {
                    fputcsv($file, (array) $row);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportToExcel(string $type, $data)
    {
        $filename = "export-{$type}-" . now()->format('Y-m-d') . ".xlsx";
        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return collect($this->data); }
        }, $filename);
    }

    public function import(string $type, $file, int $userId): int
    {
        $data = Excel::toArray([], $file)[0];
        $headers = array_shift($data);

        return $this->repository->importData($type, $headers, $data, $userId);
    }

    public function getImportHistory(int $userId)
    {
        return $this->repository->getImportHistory($userId);
    }
}
