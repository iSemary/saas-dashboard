<?php

declare(strict_types=1);

namespace Modules\CRM\Application\DTOs;

class UpdateActivityDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $subject = null,
        public readonly ?string $type = null,
        public readonly ?string $status = null,
        public readonly ?string $description = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $relatedType = null,
        public readonly ?int $relatedId = null,
        public readonly ?int $assignedTo = null,
        public readonly ?string $outcome = null,
        public readonly ?array $customFields = null,
    ) {}

    public static function fromArray(int $id, array $data): self
    {
        return new self(
            id: $id,
            subject: $data['subject'] ?? null,
            type: $data['type'] ?? null,
            status: $data['status'] ?? null,
            description: $data['description'] ?? null,
            dueDate: $data['due_date'] ?? null,
            relatedType: $data['related_type'] ?? null,
            relatedId: $data['related_id'] ?? null,
            assignedTo: $data['assigned_to'] ?? null,
            outcome: $data['outcome'] ?? null,
            customFields: $data['custom_fields'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'subject' => $this->subject,
            'type' => $this->type,
            'status' => $this->status,
            'description' => $this->description,
            'due_date' => $this->dueDate,
            'related_type' => $this->relatedType,
            'related_id' => $this->relatedId,
            'assigned_to' => $this->assignedTo,
            'outcome' => $this->outcome,
            'custom_fields' => $this->customFields,
        ], fn($v) => $v !== null);
    }
}
