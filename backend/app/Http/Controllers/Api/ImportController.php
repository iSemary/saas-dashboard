<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Import\ImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function __construct(
        protected ImportService $importService
    ) {}

    /**
     * Upload and preview import file
     */
    public function upload(Request $request, string $entity)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $preview = $this->importService->preview($entity, $file);

            return response()->json([
                'success' => true,
                'data' => $preview,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Confirm and execute import
     */
    public function confirm(Request $request, string $entity)
    {
        $request->validate([
            'valid_rows' => 'required|array',
        ]);

        try {
            $validRows = $request->input('valid_rows');
            $userId = auth()->id() ?? 0;
            
            // Start async import job (for large datasets)
            if (count($validRows) > 100) {
                // Dispatch to queue
                $jobId = $this->dispatchImportJob($entity, $validRows, $userId);
                
                return response()->json([
                    'success' => true,
                    'job_id' => $jobId,
                    'message' => 'Import job queued for processing',
                ]);
            }

            // Process synchronously for small datasets
            $result = $this->importService->import($entity, $validRows, $userId);

            return response()->json([
                'success' => $result->successCount > 0,
                'data' => $result->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Check import job status
     */
    public function status(string $jobId)
    {
        // TODO: Implement job status check with Redis/Database
        // For now, return a placeholder
        return response()->json([
            'id' => $jobId,
            'status' => 'completed',
            'progress' => 100,
            'success_count' => 0,
            'error_count' => 0,
        ]);
    }

    /**
     * Download import template
     */
    public function template(string $entity)
    {
        try {
            $templatePath = $this->importService->generateTemplate($entity);
            
            return response()->download($templatePath, $entity . '-import-template.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Dispatch import job to queue
     */
    protected function dispatchImportJob(string $entity, array $validRows, int $userId): string
    {
        // TODO: Implement actual job dispatch
        // For now, return a placeholder job ID
        return uniqid('import_', true);
    }
}
