<?php

namespace Modules\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantOwnerFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantOwnerId = $this->route('tenant_owner') ?? $this->route('id');
        
        return [
            'tenant_id' => 'required|integer|exists:tenants,id',
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('tenant_owners', 'user_id')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($tenantOwnerId)
            ],
            'role' => 'required|string|in:owner,admin,manager,user',
            'is_super_admin' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'status' => 'required|in:active,inactive,suspended',
        ];
    }

    public function messages(): array
    {
        return [
            'tenant_id.required' => 'The tenant is required.',
            'tenant_id.exists' => 'The selected tenant is invalid.',
            'user_id.required' => 'The user is required.',
            'user_id.exists' => 'The selected user is invalid.',
            'user_id.unique' => 'This user is already assigned to this tenant.',
            'role.required' => 'The role is required.',
            'role.in' => 'The role must be one of: owner, admin, manager, user.',
            'is_super_admin.boolean' => 'The super admin flag must be true or false.',
            'permissions.array' => 'The permissions must be an array.',
            'permissions.*.string' => 'Each permission must be a string.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be one of: active, inactive, suspended.',
        ];
    }

    public function attributes(): array
    {
        return [
            'tenant_id' => 'tenant',
            'user_id' => 'user',
            'role' => 'role',
            'is_super_admin' => 'super admin',
            'permissions' => 'permissions',
            'status' => 'status',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Set default values if not provided
        if (!$this->has('role')) {
            $this->merge(['role' => 'owner']);
        }

        if (!$this->has('is_super_admin')) {
            $this->merge(['is_super_admin' => false]);
        }

        if (!$this->has('status')) {
            $this->merge(['status' => 'active']);
        }

        if (!$this->has('permissions')) {
            $this->merge(['permissions' => []]);
        }
    }
}
