<?php

declare(strict_types=1);

namespace Modules\CRM\Application\DTOs;

class CreateLeadDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $company = null,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly string $status = 'new',
        public readonly ?string $source = null,
        public readonly ?float $expectedRevenue = null,
        public readonly ?string $expectedCloseDate = null,
        public readonly ?int $assignedTo = null,
        public readonly ?array $customFields = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            company: $data['company'] ?? null,
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            status: $data['status'] ?? 'new',
            source: $data['source'] ?? null,
            expectedRevenue: $data['expected_revenue'] ?? null,
            expectedCloseDate: $data['expected_close_date'] ?? null,
            assignedTo: $data['assigned_to'] ?? null,
            customFields: $data['custom_fields'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'source' => $this->source,
            'expected_revenue' => $this->expectedRevenue,
            'expected_close_date' => $this->expectedCloseDate,
            'assigned_to' => $this->assignedTo,
            'custom_fields' => $this->customFields,
        ];
    }
}
