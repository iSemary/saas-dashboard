<?php

declare(strict_types=1);

namespace Modules\CRM\Application\DTOs;

class CreateOpportunityDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?int $leadId = null,
        public readonly ?int $contactId = null,
        public readonly ?int $companyId = null,
        public readonly string $stage = 'prospecting',
        public readonly ?float $probability = null,
        public readonly ?float $expectedRevenue = null,
        public readonly ?string $expectedCloseDate = null,
        public readonly ?int $assignedTo = null,
        public readonly ?string $description = null,
        public readonly ?array $customFields = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            leadId: $data['lead_id'] ?? null,
            contactId: $data['contact_id'] ?? null,
            companyId: $data['company_id'] ?? null,
            stage: $data['stage'] ?? 'prospecting',
            probability: $data['probability'] ?? null,
            expectedRevenue: $data['expected_revenue'] ?? null,
            expectedCloseDate: $data['expected_close_date'] ?? null,
            assignedTo: $data['assigned_to'] ?? null,
            description: $data['description'] ?? null,
            customFields: $data['custom_fields'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'lead_id' => $this->leadId,
            'contact_id' => $this->contactId,
            'company_id' => $this->companyId,
            'stage' => $this->stage,
            'probability' => $this->probability,
            'expected_revenue' => $this->expectedRevenue,
            'expected_close_date' => $this->expectedCloseDate,
            'assigned_to' => $this->assignedTo,
            'description' => $this->description,
            'custom_fields' => $this->customFields,
        ], fn($v) => $v !== null);
    }
}
