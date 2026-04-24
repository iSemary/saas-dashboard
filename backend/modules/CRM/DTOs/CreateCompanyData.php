<?php

namespace Modules\CRM\DTOs;

use Modules\CRM\Http\Requests\StoreCompanyRequest;

readonly class CreateCompanyData
{
    public function __construct(
        public string $name,
        public ?string $email,
        public ?string $phone,
        public ?string $website,
        public ?string $industry,
        public ?int $employee_count,
        public ?float $annual_revenue,
        public ?string $address,
        public ?string $city,
        public ?string $state,
        public ?string $postal_code,
        public ?string $country,
        public ?string $description,
        public ?string $notes,
        public ?string $type,
        public ?int $assigned_to,
        public ?array $custom_fields,
    ) {}

    public static function fromRequest(StoreCompanyRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return [
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
        ];
    }
}
