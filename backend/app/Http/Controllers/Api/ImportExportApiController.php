<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImportExportService;
use Illuminate\Http\Request;

class ImportExportApiController extends Controller
{
    public function __construct(protected ImportExportService $importExportService) {}

    public function export(Request $request)
    {
        try {
            $type = $request->get('type', 'customers');
            $format = $request->get('format', 'csv');

            $data = $this->importExportService->getExportData($type);

            if ($format === 'csv') {
                return $this->importExportService->exportToCsv($type, $data);
            } else {
                return $this->importExportService->exportToExcel($type, $data);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
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
            $userId = $request->user('api')->id;

            $imported = $this->importExportService->import($type, $file, $userId);

            return response()->json([
                'message' => translate('message.import_success'),
                'data' => ['imported' => $imported]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function importHistory(Request $request)
    {
        try {
            $history = $this->importExportService->getImportHistory($request->user('api')->id);
            return response()->json(['data' => $history]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
