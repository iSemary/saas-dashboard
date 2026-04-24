<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:255',
            'employee_count' => 'nullable|integer|min:0',
            'annual_revenue' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'type' => 'nullable|in:customer,prospect,partner,vendor,competitor',
            'assigned_to' => 'nullable|exists:users,id',
            'custom_fields' => 'nullable|array',
        ];
    }
}
