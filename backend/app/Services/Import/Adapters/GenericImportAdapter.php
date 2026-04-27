<?php

namespace App\Services\Import\Adapters;

use App\Services\Import\AbstractImportAdapter;
use Illuminate\Database\Eloquent\Model;

class GenericImportAdapter extends AbstractImportAdapter
{
    protected string $entity;
    protected string $modelClass;
    protected array $fieldMap;
    protected array $uniqueFields;

    public function __construct(string $entity, string $modelClass, array $fieldMap = [], array $uniqueFields = [])
    {
        $this->entity = $entity;
        $this->modelClass = $modelClass;
        $this->fieldMap = $fieldMap;
        $this->uniqueFields = $uniqueFields;

        // Set required and optional fields based on fieldMap
        $this->requiredFields = $fieldMap['required'] ?? ['name'];
        $this->optionalFields = $fieldMap['optional'] ?? [];
        $this->fieldLabels = $fieldMap['labels'] ?? [];
        $this->fieldRules = $fieldMap['rules'] ?? [];
    }

    public function getEntityName(): string
    {
        return $this->entity;
    }

    public function transformRow(array $row): array
    {
        $data = parent::transformRow($row);

        // Map field names if needed
        if (!empty($this->fieldMap['mapping'])) {
            foreach ($this->fieldMap['mapping'] as $from => $to) {
                if (isset($data[$from])) {
                    $data[$to] = $data[$from];
                    unset($data[$from]);
                }
            }
        }

        // Set default values
        if (!empty($this->fieldMap['defaults'])) {
            foreach ($this->fieldMap['defaults'] as $field => $value) {
                if (empty($data[$field])) {
                    $data[$field] = $value;
                }
            }
        }

        // Handle status normalization
        if (isset($data['status'])) {
            $data['status'] = $this->normalizeStatus($data['status']);
        }

        // Handle boolean fields
        if (!empty($this->fieldMap['booleans'])) {
            foreach ($this->fieldMap['booleans'] as $field) {
                if (isset($data[$field])) {
                    $data[$field] = $this->normalizeBoolean($data[$field]);
                }
            }
        }

        return $data;
    }

    protected function customValidate(array $row, int $rowNumber): array
    {
        $errors = [];

        // Check unique fields
        foreach ($this->uniqueFields as $field) {
            if (!empty($row[$field])) {
                $exists = $this->modelClass::where($field, $row[$field])->exists();
                if ($exists) {
                    $label = $this->fieldLabels[$field] ?? $field;
                    $errors[] = "{$label} '{$row[$field]}' already exists";
                }
            }
        }

        // Check foreign key references
        if (!empty($this->fieldMap['relations'])) {
            foreach ($this->fieldMap['relations'] as $field => $config) {
                if (!empty($row[$field])) {
                    $relatedModel = $config['model'];
                    $exists = $relatedModel::where($config['field'] ?? 'id', $row[$field])->exists();
                    if (!$exists) {
                        $label = $this->fieldLabels[$field] ?? $field;
                        $errors[] = "{$label} '{$row[$field]}' does not exist";
                    }
                }
            }
        }

        return $errors;
    }

    public function importRow(array $data, int $userId): ?int
    {
        try {
            $record = $this->modelClass::create($data);
            return $record->id;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function normalizeStatus(string $status): string
    {
        $status = strtolower(trim($status));
        $mapping = [
            'active' => ['active', '1', 'yes', 'true', 'enabled'],
            'inactive' => ['inactive', '0', 'no', 'false', 'disabled'],
        ];

        foreach ($mapping as $normalized => $variants) {
            if (in_array($status, $variants)) {
                return $normalized;
            }
        }

        return $status;
    }

    protected function normalizeBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'yes', 'true', 'active', 'enabled']);
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        return (bool) $value;
    }

    protected function getSampleValue(string $field): string
    {
        $samples = [
            'name' => 'Sample Name',
            'title' => 'Sample Title',
            'code' => 'CODE001',
            'email' => 'sample@example.com',
            'phone' => '+1234567890',
            'description' => 'Sample description',
            'status' => 'active',
            'address' => '123 Sample St',
            'city' => 'Sample City',
            'country' => 'Sample Country',
            'website' => 'https://example.com',
            'notes' => 'Sample notes',
            'slug' => 'sample-slug',
        ];

        return $samples[$field] ?? '';
    }
}
