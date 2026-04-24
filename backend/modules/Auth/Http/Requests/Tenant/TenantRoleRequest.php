<?php

namespace Modules\Auth\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class TenantRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $roleId = $this->route('id') ?? $this->route('role');

        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z_]+$/',
            ],
            'guard_name' => 'sometimes|string|in:web,api',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ];

        // Handle unique validation for name
        if ($roleId)
        {
            $rules['name'][] = 'unique:roles,name,' . $roleId;
        }
        else
        {
            $rules['name'][] = 'unique:roles,name';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => @translate('role_name_required'),
            'name.string' => @translate('role_name_must_be_string'),
            'name.max' => @translate('role_name_max_255_characters'),
            'name.regex' => @translate('role_name_must_be_lowercase_and_underscores'),
            'name.unique' => @translate('role_name_already_exists'),
            'guard_name.in' => @translate('guard_name_must_be_web_or_api'),
            'permissions.array' => @translate('permissions_must_be_array'),
            'permissions.*.exists' => @translate('selected_permission_does_not_exist'),
        ];
    }

    protected function prepareForValidation(): void
    {
        // Set default guard_name if not provided
        if (!$this->has('guard_name'))
        {
            $this->merge(['guard_name' => 'web']);
        }

        // Clean the name
        if ($this->has('name'))
        {
            $this->merge([
                'name' => strtolower(str_replace(' ', '_', $this->name))
            ]);
        }
    }
}
