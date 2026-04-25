<?php

declare(strict_types=1);

namespace Modules\CRM\Application\DTOs;

class CreateActivityDTO
{
    public function __construct(
        public readonly string $subject,
        public readonly string $type = 'task',
        public readonly string $status = 'planned',
        public readonly ?string $description = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $relatedType = null,
        public readonly ?int $relatedId = null,
        public readonly ?int $assignedTo = null,
        public readonly ?string $outcome = null,
        public readonly ?array $customFields = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            subject: $data['subject'],
            type: $data['type'] ?? 'task',
            status: $data['status'] ?? 'planned',
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
