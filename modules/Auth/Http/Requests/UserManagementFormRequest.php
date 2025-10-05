<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserManagementFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['landlord', 'super_admin']) || $this->user()->hasPermissionTo('create.users');
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->route('user') ? $this->route('user')->id : 'NULL'),
            'username' => 'required|string|max:255|unique:users,username,' . ($this->route('user') ? $this->route('user')->id : 'NULL'),
            'status' => 'sometimes|in:active,inactive',
            'country_id' => 'sometimes|exists:countries,id',
            'language_id' => 'sometimes|exists:languages,id',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
            'meta' => 'sometimes|array',
        ];

        // Password rules for creation
        if ($this->isMethod('post')) 
        {
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['password_confirmation'] = 'required';
        }

        // Password rules for updates (optional)
        if ($this->isMethod('put') || $this->isMethod('patch')) 
        {
            $rules['password'] = 'sometimes|string|min:8|confirmed';
            $rules['password_confirmation'] = 'required_with:password';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('name_required'),
            'email.required' => translate('email_required'),
            'email.email' => translate('email_format_invalid'),
            'email.unique' => translate('email_already_exists'),
            'username.required' => translate('username_required'),
            'username.unique' => translate('username_already_exists'),
            'password.required' => translate('password_required'),
            'password.min' => translate('password_min_length'),
            'password.confirmed' => translate('password_confirmation_mismatch'),
            'password_confirmation.required' => translate('password_confirmation_required'),
            'password_confirmation.required_with' => translate('password_confirmation_required_with_password'),
            'status.in' => translate('status_invalid'),
            'country_id.exists' => translate('country_not_found'),
            'language_id.exists' => translate('language_not_found'),
            'roles.array' => translate('roles_must_be_array'),
            'roles.*.exists' => translate('role_not_found'),
            'permissions.array' => translate('permissions_must_be_array'),
            'permissions.*.exists' => translate('permission_not_found'),
            'meta.array' => translate('meta_must_be_array'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower($this->email),
            'username' => strtolower($this->username),
        ]);
    }
}
