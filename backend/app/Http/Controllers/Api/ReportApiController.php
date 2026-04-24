<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function index(Request $request)
    {
        try {
            $type = $request->get('type', 'customers');
            $format = $request->get('format', 'json');
            $filters = $request->only(['date_from', 'date_to', 'status']);

            $data = $this->reportService->generateReport($type, $filters);

            switch ($format) {
                case 'pdf':
                    return $this->reportService->exportToPdf($type, $data);
                case 'excel':
                    return $this->reportService->exportToExcel($type, $data);
                case 'csv':
                    return $this->reportService->exportToCsv($type, $data);
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
}
