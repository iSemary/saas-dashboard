<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $type = $request->get('type', 'customers');
            $format = $request->get('format', 'json');
            $filters = $request->only(['date_from', 'date_to', 'status']);

            $data = $this->generateReportData($type, $filters);

            switch ($format) {
                case 'pdf':
                    return $this->exportToPdf($type, $data);
                case 'excel':
                    return $this->exportToExcel($type, $data);
                case 'csv':
                    return $this->exportToCsv($type, $data);
                default:
                    return response()->json(['data' => $data]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function generateReportData(string $type, array $filters)
    {
        switch ($type) {
            case 'customers':
                $query = DB::table('companies')->where('type', 'customer');
                if (isset($filters['date_from'])) {
                    $query->where('created_at', '>=', $filters['date_from']);
                }
                if (isset($filters['date_to'])) {
                    $query->where('created_at', '<=', $filters['date_to']);
                }
                return $query->get();
            case 'tickets':
                $query = DB::table('tickets');
                if (isset($filters['status'])) {
                    $query->where('status', $filters['status']);
                }
                return $query->get();
            default:
                return [];
        }
    }

    protected function exportToPdf(string $type, $data)
    {
        $pdf = Pdf::loadView('reports.pdf', [
            'type' => $type,
            'data' => $data,
            'generated_at' => now(),
        ]);
        return $pdf->download("report-{$type}-" . now()->format('Y-m-d') . ".pdf");
    }

    protected function exportToExcel(string $type, $data)
    {
        // Simple Excel export
        $filename = "report-{$type}-" . now()->format('Y-m-d') . ".xlsx";
        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return collect($this->data); }
        }, $filename);
    }

    protected function exportToCsv(string $type, $data)
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
