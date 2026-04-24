<?php

namespace Modules\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain',
            'database_name' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
