<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class ImportExportApiController extends Controller
{
    public function export(Request $request)
    {
        try {
            $type = $request->get('type', 'customers');
            $format = $request->get('format', 'csv');

            $data = $this->getExportData($type);

            if ($format === 'csv') {
                return $this->exportToCsv($type, $data);
            } else {
                return $this->exportToExcel($type, $data);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to export data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string',
                'file' => 'required|file|mimes:csv,xlsx,xls',
            ]);

            $type = $request->get('type');
            $file = $request->file('file');

            $data = Excel::toArray([], $file)[0];
            $headers = array_shift($data);

            $imported = $this->importData($type, $headers, $data);

            return response()->json([
                'message' => "Successfully imported {$imported} records",
                'data' => ['imported' => $imported]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to import data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function importHistory(Request $request)
    {
        try {
            $history = DB::table('import_history')
                ->where('created_by', $request->user('api')->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json(['data' => $history]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve import history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function getExportData(string $type)
    {
        switch ($type) {
            case 'customers':
                return DB::table('companies')->where('type', 'customer')->get();
            case 'tickets':
                return DB::table('tickets')->get();
            default:
                return [];
        }
    }

    protected function exportToCsv(string $type, $data)
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

    protected function exportToExcel(string $type, $data)
    {
        $filename = "export-{$type}-" . now()->format('Y-m-d') . ".xlsx";
        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return collect($this->data); }
        }, $filename);
    }

    protected function importData(string $type, array $headers, array $data)
    {
        $imported = 0;
        foreach ($data as $row) {
            $record = array_combine($headers, $row);
            try {
                switch ($type) {
                    case 'customers':
                        DB::table('companies')->insert($record);
                        break;
                    case 'tickets':
                        DB::table('tickets')->insert($record);
                        break;
                }
                $imported++;
            } catch (\Exception $e) {
                // Skip invalid rows
                continue;
            }
        }

        // Log import history
        DB::table('import_history')->insert([
            'type' => $type,
            'imported_count' => $imported,
            'created_by' => request()->user('api')->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $imported;
    }
}
