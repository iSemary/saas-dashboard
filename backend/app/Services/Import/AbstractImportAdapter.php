<?php

namespace App\Services\Import;

use Illuminate\Support\Facades\Validator;

abstract class AbstractImportAdapter implements ImportAdapterInterface
{
    protected array $requiredFields = [];
    protected array $optionalFields = [];
    protected array $fieldRules = [];
    protected array $fieldLabels = [];

    public function getRequiredHeaders(): array
    {
        return $this->requiredFields;
    }

    public function getSupportedHeaders(): array
    {
        return array_merge($this->requiredFields, $this->optionalFields);
    }

    public function validateRow(array $row, int $rowNumber): array
    {
        $errors = [];
        $headers = array_keys($row);
        $supportedHeaders = $this->getSupportedHeaders();

        // Check for missing required fields
        foreach ($this->requiredFields as $requiredField) {
            if (!in_array($requiredField, $headers) || empty($row[$requiredField])) {
                $label = $this->fieldLabels[$requiredField] ?? $requiredField;
                $errors[] = "{$label} is required";
            }
        }

        // Check for unsupported columns
        foreach ($headers as $header) {
            if (!in_array($header, $supportedHeaders) && !empty($header)) {
                $errors[] = "Unknown column: {$header}";
            }
        }

        // Run validation rules if any
        if (!empty($this->fieldRules)) {
            $rules = [];
            foreach ($this->fieldRules as $field => $rule) {
                if (in_array($field, $headers)) {
                    $rules[$field] = $rule;
                }
            }

            if (!empty($rules)) {
                $validator = Validator::make($row, $rules, [], $this->fieldLabels);
                if ($validator->fails()) {
                    $errors = array_merge($errors, $validator->errors()->all());
                }
            }
        }

        // Run custom validation
        $customErrors = $this->customValidate($row, $rowNumber);
        $errors = array_merge($errors, $customErrors);

        return array_unique($errors);
    }

    /**
     * Override this method for custom validation logic
     */
    protected function customValidate(array $row, int $rowNumber): array
    {
        return [];
    }

    public function transformRow(array $row): array
    {
        // By default, return the row as-is
        // Override in specific adapters for custom transformations
        return $this->sanitizeRow($row);
    }

    /**
     * Sanitize row data
     */
    protected function sanitizeRow(array $row): array
    {
        $sanitized = [];
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    /**
     * Map header aliases to standard field names
     */
    protected function mapHeaders(array $row): array
    {
        return $row;
    }

    public function getTemplateData(): array
    {
        $headers = $this->getSupportedHeaders();
        
        // Create a sample row with field labels as comments
        $sampleRow = [];
        foreach ($headers as $header) {
            $sampleRow[$header] = $this->getSampleValue($header);
        }

        return [
            $headers,
            $sampleRow,
        ];
    }

    /**
     * Get sample value for a field
     */
    protected function getSampleValue(string $field): string
    {
        $samples = [
            'name' => 'Sample Name',
            'code' => 'CODE001',
            'email' => 'sample@example.com',
            'phone' => '+1234567890',
            'status' => 'active',
            'description' => 'Sample description',
            'address' => '123 Sample St',
            'city' => 'Sample City',
            'country' => 'Sample Country',
            'brand_id' => '1',
            'brand' => 'Brand Name',
        ];

        return $samples[$field] ?? '';
    }

    abstract public function getEntityName(): string;
}
