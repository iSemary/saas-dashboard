<?php

namespace Modules\Customer\Services;

use Modules\Customer\DTOs\CreateBranchData;
use Modules\Customer\DTOs\UpdateBranchData;
use Modules\Customer\Entities\Branch;
use Modules\Customer\Repository\BranchRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Customer\Imports\BranchImport;

class BranchService
{
    protected BranchRepositoryInterface $branchRepository;

    public function __construct(BranchRepositoryInterface $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    public function getAll(array $params = []): LengthAwarePaginator|Collection
    {
        return $this->branchRepository->getAll($params);
    }

    public function getById(int $id): ?Branch
    {
        return $this->branchRepository->getById($id);
    }

    public function findOrFail(int $id): Branch
    {
        return Branch::findOrFail($id);
    }

    public function getByCode(string $code): ?Branch
    {
        return $this->branchRepository->getByCode($code);
    }

    public function create(CreateBranchData $data): Branch
    {
        $arrayData = [
            'name' => $data->name,
            'slug' => $data->slug,
            'brand_id' => $data->brand_id,
            'is_active' => $data->is_active ?? true,
        ];

        // Generate unique code if not provided
        if (empty($arrayData['code'])) {
            $arrayData['code'] = $this->generateUniqueCode($arrayData['name'], $arrayData['brand_id']);
        }

        return $this->branchRepository->create($arrayData);
    }

    public function update(int $id, UpdateBranchData $data): bool
    {
        $branch = $this->getById($id);
        if (!$branch) {
            return false;
        }

        $arrayData = $data->toArray();

        // Generate unique code if not provided and name changed
        if (empty($arrayData['code']) && isset($arrayData['name']) && $arrayData['name'] !== $branch->name) {
            $arrayData['code'] = $this->generateUniqueCode($arrayData['name'], $branch->brand_id, $id);
        }

        return $this->branchRepository->update($id, $arrayData);
    }

    public function delete(int $id): bool
    {
        return $this->branchRepository->delete($id);
    }

    public function restore(int $id): bool
    {
        return $this->branchRepository->restore($id);
    }

    public function getByBrand(int $brandId): Collection
    {
        return $this->branchRepository->getByBrand($brandId);
    }

    public function search(string $query): Collection
    {
        return $this->branchRepository->search($query);
    }

    public function getDashboardStats(): array
    {
        return $this->branchRepository->getDashboardStats();
    }

    /**
     * Get branches for a specific brand with pagination
     */
    public function getBranchesForBrand(int $brandId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $filters['brand_id'] = $brandId;
        return $this->getAll($filters, $perPage);
    }

    /**
     * Validate branch code uniqueness within brand
     */
    public function isCodeUnique(string $code, int $brandId, ?int $excludeId = null): bool
    {
        $query = Branch::where('code', $code)
            ->where('brand_id', $brandId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->count() === 0;
    }

    /**
     * Generate unique code for branch
     */
    public function generateUniqueCode(string $name, int $brandId, ?int $excludeId = null): string
    {
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        $code = $baseCode;
        $counter = 1;

        while (!$this->isCodeUnique($code, $brandId, $excludeId)) {
            $code = $baseCode . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $code;
    }

    /**
     * Get active branches
     */
    public function getActiveBranches(): Collection
    {
        return $this->branchRepository->getActiveBranches();
    }

    /**
     * Get branches by location
     */
    public function getBranchesByLocation(string $city = null, string $state = null, string $country = null): Collection
    {
        return $this->branchRepository->getBranchesByLocation($city, $state, $country);
    }

    /**
     * Import branches from Excel file
     */
    public function importFromExcel(UploadedFile $file): array
    {
        try {
            $import = new BranchImport();
            Excel::import($import, $file);

            return [
                'success' => true,
                'message' => translate('branches_imported_successfully'),
                'imported_count' => $import->getImportedCount(),
                'failed_count' => $import->getFailedCount(),
                'errors' => $import->getErrors(),
                'valid_rows' => $import->getValidRows(),
                'invalid_rows' => $import->getInvalidRows()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => translate('import_failed') . ': ' . $e->getMessage(),
                'imported_count' => 0,
                'failed_count' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Process bulk import data
     */
    public function processBulkImport(array $branchesData): array
    {
        try {
            $results = $this->bulkCreate($branchesData);

            return [
                'success' => true,
                'message' => translate('branches_imported_successfully'),
                'imported_count' => count($results['success']),
                'failed_count' => count($results['failed']),
                'errors' => $results['failed']
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => translate('import_failed') . ': ' . $e->getMessage(),
                'imported_count' => 0,
                'failed_count' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Download Excel template for branches
     */
    public function downloadTemplate(): string
    {
        $templatePath = storage_path('app/templates/branches_template.xlsx');

        // Create template if it doesn't exist
        if (!file_exists($templatePath)) {
            $this->createTemplate($templatePath);
        }

        return $templatePath;
    }

    /**
     * Create Excel template for branches
     */
    private function createTemplate(string $path): void
    {
        $templateData = [
            ['name', 'code', 'description', 'address', 'city', 'state', 'country', 'postal_code', 'phone', 'email', 'website', 'manager_name', 'manager_email', 'manager_phone', 'latitude', 'longitude', 'status', 'brand_id'],
            ['Main Branch', 'MAIN', 'Main branch description', '123 Main St', 'New York', 'NY', 'USA', '10001', '+1-555-1234', 'main@example.com', 'https://example.com', 'John Doe', 'john@example.com', '+1-555-5678', '40.7128', '-74.0060', 'active', '1'],
            ['Downtown Branch', 'DOWN', 'Downtown branch description', '456 Downtown Ave', 'New York', 'NY', 'USA', '10002', '+1-555-2345', 'downtown@example.com', 'https://example.com', 'Jane Smith', 'jane@example.com', '+1-555-6789', '40.7589', '-73.9851', 'active', '1']
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($templateData as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $value);
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Ensure directory exists
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $writer->save($path);
    }

    /**
     * Bulk create branches
     */
    public function bulkCreate(array $branchesData): array
    {
        $results = [
            'success' => [],
            'failed' => [],
            'total' => count($branchesData)
        ];

        foreach ($branchesData as $index => $branchData) {
            try {
                $branch = $this->create($branchData);
                $results['success'][] = [
                    'index' => $index,
                    'branch' => $branch,
                    'message' => translate('branch_created_successfully')
                ];
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'index' => $index,
                    'data' => $branchData,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Get branches statistics for dashboard
     */
    public function getBranchesStatistics(): array
    {
        $stats = $this->getDashboardStats();

        return [
            'total_branches' => $stats['total'],
            'active_branches' => $stats['active'],
            'inactive_branches' => $stats['inactive'],
            'suspended_branches' => $stats['suspended'],
            'recent_branches' => $stats['recent_30_days'],
            'branches_by_brand' => $stats['by_brand'],
            'branches_by_location' => $stats['by_location']
        ];
    }
}
