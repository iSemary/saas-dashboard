<?php

namespace Modules\Customer\Imports;

use Modules\Customer\Entities\Branch;
use Modules\Customer\Entities\Brand;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BranchImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    private $importedCount = 0;
    private $failedCount = 0;
    private $errors = [];
    private $validRows = [];
    private $invalidRows = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                // Validate the row data
                $validator = Validator::make($row->toArray(), $this->rules());
                
                if ($validator->fails()) {
                    $this->failedCount++;
                    $this->errors[] = [
                        'row' => $index + 2, // +2 because of header row and 0-based index
                        'errors' => $validator->errors()->toArray()
                    ];
                    continue;
                }

                // Check if brand exists
                $brand = Brand::find($row['brand_id']);
                if (!$brand) {
                    $this->failedCount++;
                    $this->errors[] = [
                        'row' => $index + 2,
                        'errors' => ['brand_id' => ['Brand not found']]
                    ];
                    $this->invalidRows[] = $row->toArray();
                    continue;
                }

                // Generate unique code if not provided
                $code = $row['code'] ?? $this->generateUniqueCode($row['name'], $row['brand_id']);

                // Prepare branch data
                $branchData = [
                    'name' => $row['name'],
                    'code' => $code,
                    'description' => $row['description'] ?? null,
                    'address' => $row['address'] ?? null,
                    'city' => $row['city'] ?? null,
                    'state' => $row['state'] ?? null,
                    'country' => $row['country'] ?? null,
                    'postal_code' => $row['postal_code'] ?? null,
                    'phone' => $row['phone'] ?? null,
                    'email' => $row['email'] ?? null,
                    'website' => $row['website'] ?? null,
                    'manager_name' => $row['manager_name'] ?? null,
                    'manager_email' => $row['manager_email'] ?? null,
                    'manager_phone' => $row['manager_phone'] ?? null,
                    'latitude' => $row['latitude'] ?? null,
                    'longitude' => $row['longitude'] ?? null,
                    'status' => $row['status'] ?? 'active',
                    'brand_id' => $row['brand_id'],
                    'created_by' => auth()->id(),
                ];

                // Add brand name for preview
                $branchData['brand_name'] = $brand->name;

                $this->validRows[] = $branchData;
                $this->importedCount++;
            } catch (\Exception $e) {
                $this->failedCount++;
                $this->errors[] = [
                    'row' => $index + 2,
                    'errors' => ['general' => [$e->getMessage()]]
                ];
                $this->invalidRows[] = $row->toArray();
            }
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'manager_name' => 'nullable|string|max:255',
            'manager_email' => 'nullable|email|max:255',
            'manager_phone' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'nullable|in:active,inactive,suspended',
            'brand_id' => 'required|exists:brands,id',
        ];
    }

    private function generateUniqueCode(string $name, int $brandId): string
    {
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        $code = $baseCode;
        $counter = 1;

        while (Branch::where('code', $code)->where('brand_id', $brandId)->exists()) {
            $code = $baseCode . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $code;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getFailedCount(): int
    {
        return $this->failedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getValidRows(): array
    {
        return $this->validRows;
    }

    public function getInvalidRows(): array
    {
        return $this->invalidRows;
    }
}
