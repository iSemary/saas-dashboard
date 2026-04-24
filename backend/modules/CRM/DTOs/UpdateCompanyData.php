<?php

namespace Modules\CRM\DTOs;

use Modules\CRM\Http\Requests\UpdateCompanyRequest;

readonly class UpdateCompanyData
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $website = null,
        public ?string $industry = null,
        public ?int $employee_count = null,
        public ?float $annual_revenue = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $postal_code = null,
        public ?string $country = null,
        public ?string $description = null,
        public ?string $notes = null,
        public ?string $type = null,
        public ?int $assigned_to = null,
        public ?array $custom_fields = null,
    ) {}

    public static function fromRequest(UpdateCompanyRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'industry' => $this->industry,
            'employee_count' => $this->employee_count,
            'annual_revenue' => $this->annual_revenue,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'description' => $this->description,
            'notes' => $this->notes,
            'type' => $this->type,
            'assigned_to' => $this->assigned_to,
            'custom_fields' => $this->custom_fields,
        ], fn ($value) => $value !== null);
    }
}
