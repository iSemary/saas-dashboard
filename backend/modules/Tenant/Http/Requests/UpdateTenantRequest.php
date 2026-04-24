<?php

namespace Modules\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:255',
            'domain' => "sometimes|required|string|max:255|unique:tenants,domain,{$id}",
            'is_active' => 'nullable|boolean',
        ];
    }
}
