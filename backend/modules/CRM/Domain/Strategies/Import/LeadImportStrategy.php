<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\Import;

use Modules\CRM\Domain\Entities\Lead;
use Modules\CRM\Domain\ValueObjects\LeadStatus;
use Modules\CRM\Domain\ValueObjects\LeadSource;
use Modules\CRM\Infrastructure\Persistence\LeadRepositoryInterface;

class LeadImportStrategy implements ImportStrategyInterface
{
    public function __construct(
        private LeadRepositoryInterface $repository
    ) {
    }

    public function supports(string $entityType): bool
    {
        return $entityType === 'lead';
    }

    public function validateRow(array $row, array $mapping): array
    {
        $errors = [];
        $data = $this->applyMapping($row, $mapping);

        // Required fields
        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        }

        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        // Status validation
        if (!empty($data['status']) && !LeadStatus::tryFrom($data['status'])) {
            $validStatuses = implode(', ', array_column(LeadStatus::cases(), 'value'));
            $errors[] = "Invalid status '{$data['status']}'. Valid: {$validStatuses}";
        }

        // Source validation
        if (!empty($data['source']) && !LeadSource::tryFrom($data['source'])) {
            $validSources = implode(', ', array_column(LeadSource::cases(), 'value'));
            $errors[] = "Invalid source '{$data['source']}'. Valid: {$validSources}";
        }

        // Revenue validation
        if (!empty($data['expected_revenue']) && !is_numeric($data['expected_revenue'])) {
            $errors[] = 'Expected revenue must be numeric';
        }

        return $errors;
    }

    public function processRow(array $row, array $mapping, int $importJobId): bool
    {
        $data = $this->applyMapping($row, $mapping);

        // Set defaults
        $data['status'] = $data['status'] ?? LeadStatus::NEW->value;
        $data['source'] = $data['source'] ?? LeadSource::OTHER->value;
        $data['import_job_id'] = $importJobId;
        $data['created_by'] = auth()->id();

        // Convert numeric fields
        if (!empty($data['expected_revenue'])) {
            $data['expected_revenue'] = (float) $data['expected_revenue'];
        }

        try {
            $this->repository->create($data);
            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public function getRequiredFields(): array
    {
        return ['name'];
    }

    public function getAvailableFields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'required' => true, 'type' => 'string'],
            ['name' => 'email', 'label' => 'Email', 'required' => false, 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Phone', 'required' => false, 'type' => 'string'],
            ['name' => 'company', 'label' => 'Company', 'required' => false, 'type' => 'string'],
            ['name' => 'title', 'label' => 'Job Title', 'required' => false, 'type' => 'string'],
            ['name' => 'status', 'label' => 'Status', 'required' => false, 'type' => 'enum', 'options' => LeadStatus::all()],
            ['name' => 'source', 'label' => 'Source', 'required' => false, 'type' => 'enum', 'options' => LeadSource::all()],
            ['name' => 'expected_revenue', 'label' => 'Expected Revenue', 'required' => false, 'type' => 'number'],
            ['name' => 'expected_close_date', 'label' => 'Expected Close Date', 'required' => false, 'type' => 'date'],
            ['name' => 'assigned_to', 'label' => 'Assigned To (User ID)', 'required' => false, 'type' => 'number'],
            ['name' => 'description', 'label' => 'Description', 'required' => false, 'type' => 'text'],
        ];
    }

    public function getDefaultMapping(): array
    {
        return [
            0 => 'name',
            1 => 'email',
            2 => 'phone',
            3 => 'company',
            4 => 'status',
        ];
    }

    public function getSampleData(): array
    {
        return [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1-555-0123',
            'company' => 'Acme Corp',
            'title' => 'CEO',
            'status' => 'new',
            'source' => 'website',
            'expected_revenue' => '5000.00',
            'expected_close_date' => '2025-06-30',
            'assigned_to' => '1',
            'description' => 'Interested in enterprise plan',
        ];
    }

    public function getEntityName(): string
    {
        return 'Lead';
    }

    public function checkDuplicate(array $row, array $mapping): ?int
    {
        $data = $this->applyMapping($row, $mapping);

        // Check by email
        if (!empty($data['email'])) {
            $existing = Lead::where('email', $data['email'])->first();
            if ($existing) {
                return $existing->id;
            }
        }

        // Check by phone
        if (!empty($data['phone'])) {
            $existing = Lead::where('phone', $data['phone'])->first();
            if ($existing) {
                return $existing->id;
            }
        }

        return null;
    }

    private function applyMapping(array $row, array $mapping): array
    {
        $data = [];
        foreach ($mapping as $csvIndex => $fieldName) {
            if (isset($row[$csvIndex])) {
                $data[$fieldName] = trim($row[$csvIndex]);
            }
        }

        return $data;
    }
}
