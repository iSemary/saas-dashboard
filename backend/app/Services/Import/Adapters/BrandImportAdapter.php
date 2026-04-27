<?php

namespace App\Services\Import\Adapters;

use App\Services\Import\AbstractImportAdapter;
use App\Models\Brand;

class BrandImportAdapter extends AbstractImportAdapter
{
    protected array $requiredFields = ['name'];
    protected array $optionalFields = ['code', 'email', 'phone', 'website', 'description', 'status', 'address', 'city', 'country'];
    protected array $fieldLabels = [
        'name' => 'Brand Name',
        'code' => 'Brand Code',
        'email' => 'Email',
        'phone' => 'Phone',
        'website' => 'Website',
        'description' => 'Description',
        'status' => 'Status',
        'address' => 'Address',
        'city' => 'City',
        'country' => 'Country',
    ];

    public function getEntityName(): string
    {
        return 'brands';
    }

    public function transformRow(array $row): array
    {
        $data = parent::transformRow($row);
        
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

        // Validate unique code
        if (!empty($row['code'])) {
            $existing = Brand::where('code', $row['code'])->first();
            if ($existing) {
                $errors[] = "Brand code '{$row['code']}' already exists";
            }
        }

        // Validate unique name
        if (!empty($row['name'])) {
            $existing = Brand::where('name', $row['name'])->first();
            if ($existing) {
                $errors[] = "Brand name '{$row['name']}' already exists";
            }
        }

        // Validate email format
        if (!empty($row['email']) && !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }

        // Validate website URL
        if (!empty($row['website'])) {
            if (!str_starts_with($row['website'], 'http://') && !str_starts_with($row['website'], 'https://')) {
                // Auto-fix by adding https://
                $row['website'] = 'https://' . $row['website'];
            }
            if (!filter_var($row['website'], FILTER_VALIDATE_URL)) {
                $errors[] = "Invalid website URL";
            }
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
            $brand = Brand::create($data);
            return $brand->id;
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
        while (Brand::where('code', $code)->exists()) {
            $code = $originalCode . $counter;
            $counter++;
        }
        
        return $code;
    }

    protected function getSampleValue(string $field): string
    {
        $samples = [
            'name' => 'Acme Corporation',
            'code' => 'ACM',
            'email' => 'contact@acme.com',
            'phone' => '+1234567890',
            'website' => 'https://acme.com',
            'description' => 'Leading provider of widgets',
            'status' => 'active',
            'address' => '123 Business Ave',
            'city' => 'New York',
            'country' => 'USA',
        ];

        return $samples[$field] ?? '';
    }
}
