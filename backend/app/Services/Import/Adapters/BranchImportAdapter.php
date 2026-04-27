<?php

namespace App\Services\Import\Adapters;

use App\Services\Import\AbstractImportAdapter;
use App\Models\Branch;
use App\Models\Brand;

class BranchImportAdapter extends AbstractImportAdapter
{
    protected array $requiredFields = ['name', 'brand_id'];
    protected array $optionalFields = ['code', 'email', 'phone', 'address', 'city', 'state', 'country', 'postal_code', 'status', 'description'];
    protected array $fieldLabels = [
        'name' => 'Branch Name',
        'code' => 'Branch Code',
        'brand_id' => 'Brand ID',
        'email' => 'Email',
        'phone' => 'Phone',
        'address' => 'Address',
        'city' => 'City',
        'state' => 'State/Province',
        'country' => 'Country',
        'postal_code' => 'Postal Code',
        'status' => 'Status',
        'description' => 'Description',
    ];

    public function getEntityName(): string
    {
        return 'branches';
    }

    public function transformRow(array $row): array
    {
        $data = parent::transformRow($row);
        
        // Handle brand lookup by name if brand_id is not numeric
        if (!empty($data['brand_id']) && !is_numeric($data['brand_id'])) {
            $brand = Brand::where('name', $data['brand_id'])->first();
            if ($brand) {
                $data['brand_id'] = $brand->id;
            }
        }

        // Auto-generate code if not provided
        if (empty($data['code']) && !empty($data['name'])) {
            $data['code'] = $this->generateCode($data['name']);
        }

        // Set default status
        if (empty($data['status'])) {
            $data['status'] = 'active';
        }

        return $data;
    }

    protected function customValidate(array $row, int $rowNumber): array
    {
        $errors = [];
        
        // Validate brand exists
        if (!empty($row['brand_id'])) {
            if (is_numeric($row['brand_id'])) {
                $brandExists = Brand::where('id', $row['brand_id'])->exists();
                if (!$brandExists) {
                    $errors[] = "Brand ID {$row['brand_id']} does not exist";
                }
            } else {
                // Check if brand name exists
                $brandExists = Brand::where('name', $row['brand_id'])->exists();
                if (!$brandExists) {
                    $errors[] = "Brand '{$row['brand_id']}' does not exist";
                }
            }
        }

        // Validate unique code
        if (!empty($row['code'])) {
            $existing = Branch::where('code', $row['code'])->first();
            if ($existing) {
                $errors[] = "Branch code '{$row['code']}' already exists";
            }
        }

        // Validate email format
        if (!empty($row['email']) && !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }

        // Validate status
        if (!empty($row['status']) && !in_array($row['status'], ['active', 'inactive'])) {
            $errors[] = "Status must be 'active' or 'inactive'";
        }

        return $errors;
    }

    public function importRow(array $data, int $userId): ?int
    {
        try {
            $branch = Branch::create($data);
            return $branch->id;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function generateCode(string $name): string
    {
        $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        
        // Ensure uniqueness
        $counter = 1;
        $originalCode = $code;
        while (Branch::where('code', $code)->exists()) {
            $code = $originalCode . $counter;
            $counter++;
        }
        
        return $code;
    }

    protected function getSampleValue(string $field): string
    {
        $samples = [
            'name' => 'Main Branch',
            'code' => 'MB001',
            'brand_id' => '1',
            'email' => 'main@example.com',
            'phone' => '+1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
            'postal_code' => '10001',
            'status' => 'active',
            'description' => 'Main headquarters branch',
        ];

        return $samples[$field] ?? '';
    }
}
