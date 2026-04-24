<?php

namespace Modules\Auth\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class TenantPermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $permissionId = $this->route('id') ?? $this->route('permission');

        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z_]+\.[a-z_]+$/',
            ],
            'guard_name' => 'sometimes|string|in:web,api',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id',
        ];

        // Handle unique validation for name
        if ($permissionId)
        {
            $rules['name'][] = 'unique:permissions,name,' . $permissionId;
        }
        else
        {
            $rules['name'][] = 'unique:permissions,name';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => @translate('permission_name_required'),
            'name.string' => @translate('permission_name_must_be_string'),
            'name.max' => @translate('permission_name_max_255_characters'),
            'name.regex' => @translate('permission_name_must_follow_format_action_resource'),
            'name.unique' => @translate('permission_name_already_exists'),
            'guard_name.in' => @translate('guard_name_must_be_web_or_api'),
            'roles.array' => @translate('roles_must_be_array'),
            'roles.*.exists' => @translate('selected_role_does_not_exist'),
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
                'name' => strtolower(str_replace(' ', '.', trim($this->name)))
            ]);
        }
    }
}
