<?php

namespace Modules\HR\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\HR\Domain\ValueObjects\EmploymentStatus;
use Modules\HR\Domain\ValueObjects\EmploymentType;
use Modules\HR\Domain\ValueObjects\Gender;
use Modules\HR\Domain\ValueObjects\MaritalStatus;
use Modules\HR\Domain\ValueObjects\PayFrequency;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|string|in:' . implode(',', array_column(Gender::cases(), 'value')),
            'marital_status' => 'nullable|string|in:' . implode(',', array_column(MaritalStatus::cases(), 'value')),
            'national_id' => 'nullable|string|max:100',
            'passport_number' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'hire_date' => 'required|date',
            'probation_end_date' => 'nullable|date|after_or_equal:hire_date',
            'employment_status' => 'required|string|in:' . implode(',', array_column(EmploymentStatus::cases(), 'value')),
            'employment_type' => 'required|string|in:' . implode(',', array_column(EmploymentType::cases(), 'value')),
            'department_id' => 'nullable|integer|exists:departments,id',
            'position_id' => 'nullable|integer|exists:positions,id',
            'manager_id' => 'nullable|integer|exists:employees,id',
            'salary' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'pay_frequency' => 'required|string|in:' . implode(',', array_column(PayFrequency::cases(), 'value')),
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'custom_fields' => 'nullable|array',
        ];
    }
}
