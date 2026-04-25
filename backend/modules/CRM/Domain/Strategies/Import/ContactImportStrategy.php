<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\Import;

use Modules\CRM\Domain\Entities\Contact;
use Modules\CRM\Domain\ValueObjects\CompanyType;
use Modules\CRM\Infrastructure\Persistence\ContactRepositoryInterface;

class ContactImportStrategy implements ImportStrategyInterface
{
    public function __construct(
        private ContactRepositoryInterface $repository
    ) {
    }

    public function supports(string $entityType): bool
    {
        return $entityType === 'contact';
    }

    public function validateRow(array $row, array $mapping): array
    {
        $errors = [];
        $data = $this->applyMapping($row, $mapping);

        // Required fields
        if (empty($data['first_name']) && empty($data['last_name'])) {
            $errors[] = 'Either first name or last name is required';
        }

        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        // Type validation
        if (!empty($data['type']) && !CompanyType::tryFrom($data['type'])) {
            $validTypes = implode(', ', array_column(CompanyType::cases(), 'value'));
            $errors[] = "Invalid type '{$data['type']}'. Valid: {$validTypes}";
        }

        return $errors;
    }

    public function processRow(array $row, array $mapping, int $importJobId): bool
    {
        $data = $this->applyMapping($row, $mapping);

        // Set defaults
        $data['type'] = $data['type'] ?? CompanyType::CUSTOMER->value;
        $data['import_job_id'] = $importJobId;
        $data['created_by'] = auth()->id();

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
        return ['first_name', 'last_name'];
    }

    public function getAvailableFields(): array
    {
        return [
            ['name' => 'first_name', 'label' => 'First Name', 'required' => false, 'type' => 'string'],
            ['name' => 'last_name', 'label' => 'Last Name', 'required' => false, 'type' => 'string'],
            ['name' => 'email', 'label' => 'Email', 'required' => false, 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Phone', 'required' => false, 'type' => 'string'],
            ['name' => 'mobile', 'label' => 'Mobile', 'required' => false, 'type' => 'string'],
            ['name' => 'job_title', 'label' => 'Job Title', 'required' => false, 'type' => 'string'],
            ['name' => 'company_id', 'label' => 'Company ID', 'required' => false, 'type' => 'number'],
            ['name' => 'type', 'label' => 'Type', 'required' => false, 'type' => 'enum', 'options' => CompanyType::all()],
            ['name' => 'assigned_to', 'label' => 'Assigned To (User ID)', 'required' => false, 'type' => 'number'],
            ['name' => 'address', 'label' => 'Address', 'required' => false, 'type' => 'string'],
            ['name' => 'city', 'label' => 'City', 'required' => false, 'type' => 'string'],
            ['name' => 'state', 'label' => 'State', 'required' => false, 'type' => 'string'],
            ['name' => 'country', 'label' => 'Country', 'required' => false, 'type' => 'string'],
        ];
    }

    public function getDefaultMapping(): array
    {
        return [
            0 => 'first_name',
            1 => 'last_name',
            2 => 'email',
            3 => 'phone',
            4 => 'company_id',
        ];
    }

    public function getSampleData(): array
    {
        return [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'phone' => '+1-555-0199',
            'mobile' => '+1-555-0188',
            'job_title' => 'Marketing Director',
            'company_id' => '1',
            'type' => 'customer',
            'assigned_to' => '1',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
        ];
    }

    public function getEntityName(): string
    {
        return 'Contact';
    }

    public function checkDuplicate(array $row, array $mapping): ?int
    {
        $data = $this->applyMapping($row, $mapping);

        // Check by email
        if (!empty($data['email'])) {
            $existing = Contact::where('email', $data['email'])->first();
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
