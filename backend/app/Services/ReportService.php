<?php

namespace App\Services;

use App\Repositories\ReportRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportService
{
    public function __construct(protected ReportRepositoryInterface $repository) {}

    public function generateReport(string $type, array $filters)
    {
        return match ($type) {
            'customers' => $this->repository->getCustomers($filters),
            'tickets' => $this->repository->getTickets($filters),
            default => [],
        };
    }

    public function exportToPdf(string $type, $data)
    {
        $pdf = Pdf::loadView('reports.pdf', [
            'type' => $type,
            'data' => $data,
            'generated_at' => now(),
        ]);
        return $pdf->download("report-{$type}-" . now()->format('Y-m-d') . ".pdf");
    }

    public function exportToExcel(string $type, $data)
    {
        $filename = "report-{$type}-" . now()->format('Y-m-d') . ".xlsx";
        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return collect($this->data); }
        }, $filename);
    }

    public function exportToCsv(string $type, $data)
    {
        $filename = "report-{$type}-" . now()->format('Y-m-d') . ".csv";
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
}
