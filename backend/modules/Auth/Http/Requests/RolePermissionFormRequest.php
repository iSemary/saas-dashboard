<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RolePermissionFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['landlord', 'super_admin']) || $this->user()->hasPermissionTo('manage.roles');
    }

    public function rules(): array
    {
        $rules = [];

        if ($this->isMethod('post') || $this->isMethod('put') || $this->isMethod('patch')) 
        {
            // Role rules
            if ($this->route('role')) 
            {
                $rules = [
                    'name' => 'required|string|max:255|unique:roles,name,' . ($this->route('role') ? $this->route('role')->id : 'NULL'),
                    'guard_name' => 'required|string|max:255',
                    'permissions' => 'sometimes|array',
                    'permissions.*' => 'exists:permissions,id',
                ];
            }
            // Permission rules
            elseif ($this->route('permission')) 
            {
                $rules = [
                    'name' => 'required|string|max:255|unique:permissions,name,' . ($this->route('permission') ? $this->route('permission')->id : 'NULL'),
                    'guard_name' => 'required|string|max:255',
                ];
            }
            // Default rules when creating role/permission
            else 
            {
                $rules = [
                    'type' => 'required|in:role,permission',
                    'name' => 'required|string|max:255',
                    'guard_name' => 'required|string|max:255',
                    'permissions' => 'sometimes|array',
                    'permissions.*' => 'exists:permissions,id',
                ];

                // Add unique validation based on type
                if ($this->input('type') === 'role') 
                {
                    $rules['name'] = 'required|string|max:255|unique:roles,name';
                } 
                elseif ($this->input('type') === 'permission') 
                {
                    $rules['name'] = 'required|string|max:255|unique:permissions,name';
                }
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'type.required' => translate('type_required'),
            'type.in' => translate('type_must_be_role_or_permission'),
            'name.required' => translate('name_required'),
            'name.unique' => translate('name_already_exists'),
            'guard_name.required' => translate('guard_name_required'),
            'permissions.array' => translate('permissions_must_be_array'),
            'permissions.*.exists' => translate('permission_not_found'),
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalize guard name if not provided
        if (!$this->has('guard_name')) 
        {
            $this->merge([
                'guard_name' => 'web'
            ]);
        }

        // Normalize name
        if ($this->has('name')) 
        {
            $this->merge([
                'name' => strtolower($this->name)
            ]);
        }
    }
}
