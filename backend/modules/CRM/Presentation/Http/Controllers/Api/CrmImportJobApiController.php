<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\CRM\Infrastructure\Persistence\CrmImportJobRepositoryInterface;

class CrmImportJobApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly CrmImportJobRepositoryInterface $importJobs) {}

    public function index(Request $request): JsonResponse
    {
        try {
            return $this->apiPaginated($this->importJobs->paginate([], (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve import jobs', 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:10240',
                'entity_type' => 'required|string|in:leads,contacts,companies',
                'mapping' => 'required|array',
            ]);
            
            $file = $request->file('file');
            $path = $file->store('crm/imports', 'private');
            
            $data = [
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'entity_type' => $request->input('entity_type'),
                'mapping' => $request->input('mapping'),
                'status' => 'pending',
                'created_by' => auth()->id(),
            ];
            
            $job = $this->importJobs->create($data);
            
            // Dispatch the import job to queue
            \Modules\CRM\Infrastructure\Jobs\ProcessImportJob::dispatch($job);
            
            return $this->apiSuccess($job, 'Import job created and queued', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create import job', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->importJobs->findOrFail($id));
        } catch (\Throwable $e) {
            return $this->apiError('Import job not found', 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $job = $this->importJobs->findOrFail($id);
            if ($job->file_path) {
                Storage::disk('private')->delete($job->file_path);
            }
            $this->importJobs->delete($id);
            return $this->apiSuccess(null, 'Import job deleted');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete import job', 500, $e->getMessage());
        }
    }

    public function downloadTemplate(string $entityType): JsonResponse
    {
        try {
            $headers = match($entityType) {
                'leads' => ['name', 'email', 'phone', 'company', 'title', 'status', 'source'],
                'contacts' => ['first_name', 'last_name', 'email', 'phone', 'company_id', 'title'],
                'companies' => ['name', 'email', 'phone', 'website', 'industry', 'type'],
                default => throw new \InvalidArgumentException('Invalid entity type'),
            };
            
            return $this->apiSuccess(['headers' => $headers, 'sample' => array_fill(0, count($headers), 'sample_data')]);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get template', 500, $e->getMessage());
        }
    }
}
