<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BranchFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $branchId = $this->route('branch');
        
        return [
            'name' => 'required|string|max:255',
            'code' => [
                'nullable',
                'string',
                'max:10',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('branches', 'code')->ignore($branchId)->where(function ($query) {
                    return $query->where('brand_id', $this->brand_id);
                })
            ],
            'description' => 'nullable|string|max:1000',
            'working_hours' => 'nullable|array',
            'working_hours.*' => 'nullable|array',
            'working_hours.*.open' => 'nullable|string|max:10',
            'working_hours.*.close' => 'nullable|string|max:10',
            'working_days' => 'nullable|array',
            'working_days.*' => 'nullable|boolean',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'manager_name' => 'nullable|string|max:255',
            'manager_email' => 'nullable|email|max:255',
            'manager_phone' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:active,inactive,suspended',
            'brand_id' => 'required|exists:brands,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => translate('branch_name_is_required'),
            'name.max' => translate('branch_name_must_not_exceed_255_characters'),
            'code.regex' => translate('branch_code_must_contain_only_uppercase_letters_and_numbers'),
            'code.unique' => translate('branch_code_must_be_unique_within_brand'),
            'email.email' => translate('please_enter_valid_email_address'),
            'website.url' => translate('please_enter_valid_website_url'),
            'manager_email.email' => translate('please_enter_valid_manager_email_address'),
            'latitude.between' => translate('latitude_must_be_between_minus_90_and_90'),
            'longitude.between' => translate('longitude_must_be_between_minus_180_and_180'),
            'status.required' => translate('branch_status_is_required'),
            'status.in' => translate('branch_status_must_be_active_inactive_or_suspended'),
            'brand_id.required' => translate('brand_is_required'),
            'brand_id.exists' => translate('selected_brand_does_not_exist'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => translate('branch_name'),
            'code' => translate('branch_code'),
            'description' => translate('description'),
            'working_hours' => translate('working_hours'),
            'working_days' => translate('working_days'),
            'address' => translate('address'),
            'city' => translate('city'),
            'state' => translate('state'),
            'country' => translate('country'),
            'postal_code' => translate('postal_code'),
            'phone' => translate('phone'),
            'email' => translate('email'),
            'website' => translate('website'),
            'manager_name' => translate('manager_name'),
            'manager_email' => translate('manager_email'),
            'manager_phone' => translate('manager_phone'),
            'latitude' => translate('latitude'),
            'longitude' => translate('longitude'),
            'status' => translate('status'),
            'brand_id' => translate('brand'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert code to uppercase if provided
        if ($this->has('code') && !empty($this->code)) {
            $this->merge([
                'code' => strtoupper($this->code)
            ]);
        }

        // Set default status if not provided
        if (!$this->has('status')) {
            $this->merge([
                'status' => 'active'
            ]);
        }
    }
}
