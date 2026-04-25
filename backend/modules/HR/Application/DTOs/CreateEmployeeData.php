<?php

namespace Modules\HR\Application\DTOs;

use Modules\HR\Presentation\Http\Requests\StoreEmployeeRequest;

readonly class CreateEmployeeData
{
    public function __construct(
        public string $first_name,
        public ?string $middle_name,
        public string $last_name,
        public string $email,
        public ?string $phone,
        public ?string $date_of_birth,
        public ?string $gender,
        public ?string $marital_status,
        public ?string $national_id,
        public ?string $passport_number,
        public ?string $address,
        public ?string $city,
        public ?string $state,
        public ?string $postal_code,
        public ?string $country,
        public string $hire_date,
        public ?string $probation_end_date,
        public string $employment_status,
        public string $employment_type,
        public ?int $department_id,
        public ?int $position_id,
        public ?int $manager_id,
        public ?float $salary,
        public string $currency,
        public string $pay_frequency,
        public ?string $emergency_contact_name,
        public ?string $emergency_contact_phone,
        public ?string $emergency_contact_relationship,
        public ?array $custom_fields,
    ) {}

    public static function fromRequest(StoreEmployeeRequest $request): self
    {
        return new self(
            first_name: $request->validated('first_name'),
            middle_name: $request->validated('middle_name'),
            last_name: $request->validated('last_name'),
            email: $request->validated('email'),
            phone: $request->validated('phone'),
            date_of_birth: $request->validated('date_of_birth'),
            gender: $request->validated('gender'),
            marital_status: $request->validated('marital_status'),
            national_id: $request->validated('national_id'),
            passport_number: $request->validated('passport_number'),
            address: $request->validated('address'),
            city: $request->validated('city'),
            state: $request->validated('state'),
            postal_code: $request->validated('postal_code'),
            country: $request->validated('country'),
            hire_date: $request->validated('hire_date'),
            probation_end_date: $request->validated('probation_end_date'),
            employment_status: $request->validated('employment_status', 'active'),
            employment_type: $request->validated('employment_type', 'full_time'),
            department_id: $request->validated('department_id'),
            position_id: $request->validated('position_id'),
            manager_id: $request->validated('manager_id'),
            salary: $request->validated('salary'),
            currency: $request->validated('currency', 'USD'),
            pay_frequency: $request->validated('pay_frequency', 'monthly'),
            emergency_contact_name: $request->validated('emergency_contact_name'),
            emergency_contact_phone: $request->validated('emergency_contact_phone'),
            emergency_contact_relationship: $request->validated('emergency_contact_relationship'),
            custom_fields: $request->validated('custom_fields'),
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'marital_status' => $this->marital_status,
            'national_id' => $this->national_id,
            'passport_number' => $this->passport_number,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'hire_date' => $this->hire_date,
            'probation_end_date' => $this->probation_end_date,
            'employment_status' => $this->employment_status,
            'employment_type' => $this->employment_type,
            'department_id' => $this->department_id,
            'position_id' => $this->position_id,
            'manager_id' => $this->manager_id,
            'salary' => $this->salary,
            'currency' => $this->currency,
            'pay_frequency' => $this->pay_frequency,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'emergency_contact_relationship' => $this->emergency_contact_relationship,
            'custom_fields' => $this->custom_fields,
        ];
    }
}
